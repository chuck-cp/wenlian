<?php
/**
 * @copyright Copyright (c) 2024 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Caches\CourseChapterList as CourseChapterListCache;
use App\Library\Utils\Lock as LockUtil;
use App\Models\Chapter as ChapterModel;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Repos\Chapter as ChapterRepo;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CloseLiveTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $chapterLives = $this->findChapterLives();

        echo sprintf('pending lives: %s', $chapterLives->count()) . PHP_EOL;

        if ($chapterLives->count() == 0) return;

        echo '------ start close live task ------' . PHP_EOL;

        foreach ($chapterLives as $chapterLive) {
            $this->closeChapterLive($chapterLive);
        }

        echo '------ end close live task ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function closeChapterLive(ChapterLiveModel $chapterLive)
    {
        try {

            $this->db->begin();

            $chapterLive->status = ChapterLiveModel::STATUS_INACTIVE;
            $chapterLive->update();

            $chapterRepo = new ChapterRepo();
            $chapter = $chapterRepo->findById($chapterLive->chapter_id);

            $attrs = $chapter->attrs;
            $attrs['stream']['status'] = ChapterModel::STREAM_STATUS_INACTIVE;
            $chapter->attrs = $attrs;
            $chapter->update();

            $this->db->commit();

            $cache = new CourseChapterListCache();
            $cache->rebuild($chapterLive->course_id);

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('live');

            $logger->error('Close Live Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'live' => $chapterLive,
                ]));
        }
    }

    /**
     * 查找待关闭直播
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|ChapterLiveModel[]
     */
    protected function findChapterLives($limit = 100)
    {
        $status = ChapterLiveModel::STATUS_ACTIVE;
        $endTime = time() - 3600;

        return ChapterLiveModel::query()
            ->where('status = :status:', ['status' => $status])
            ->andWhere('end_time < :end_time:', ['end_time' => $endTime])
            ->limit($limit)
            ->execute();
    }

}
