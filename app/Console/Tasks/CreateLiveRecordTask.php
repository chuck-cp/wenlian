<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Services\Live as LiveService;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CreateLiveRecordTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $lives = $this->findLives();

        echo sprintf('pending lives: %s', $lives->count()) . PHP_EOL;

        if ($lives->count() == 0) return;

        echo '------ start create live record task ------' . PHP_EOL;

        $liveService = new LiveService();

        foreach ($lives as $live) {
            if ($live->settings['record_enabled'] == 1) {
                $streamName = ChapterLiveModel::generateStreamName($live->chapter_id);
                $liveService->createRecordTask($live->start_time, $live->end_time, $streamName);
            }
        }

        echo '------ end create live record task ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    /**
     * 查找待录制直播
     *
     * @return ResultsetInterface|Resultset|ChapterLiveModel[]
     */
    protected function findLives()
    {
        $min = strtotime('today');
        $max = strtotime('+1 day', $min);

        return ChapterLiveModel::query()
            ->betweenWhere('start_time', $min, $max)
            ->execute();
    }

}
