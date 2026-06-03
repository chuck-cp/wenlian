<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services;

use App\Caches\Chapter as ChapterCache;
use App\Caches\CourseChapterList as CourseChapterListCache;
use App\Models\Chapter as ChapterModel;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Models\ChapterVod as ChapterVodModel;
use App\Repos\Chapter as ChapterRepo;
use App\Services\Logic\Notice\External\LiveBegin as LiveBeginNotice;
use Phalcon\Logger\Adapter\File as FileLogger;

class LiveNotify extends Service
{

    /**
     * @var FileLogger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = $this->getLogger('live');
    }

    public function handle()
    {
        $time = $this->request->getPost('t', 'int');
        $sign = $this->request->getPost('sign', 'string');
        $action = $this->request->getQuery('action', 'string');

        if (!$this->checkSign($sign, $time)) {
            $this->logger->error('Live Notify Sign Error: ' . kg_json_encode([
                    't' => $time,
                    'sign' => $sign,
                    'action' => $action,
                ]));
            return false;
        }

        $result = false;

        switch ($action) {
            case 'streamBegin':
                $result = $this->handleStreamBegin();
                break;
            case 'streamEnd':
                $result = $this->handleStreamEnd();
                break;
            case 'record':
                $result = $this->handleRecord();
                break;
            case 'snapshot':
                $result = $this->handleSnapshot();
                break;
            case 'porn':
                $result = $this->handlePorn();
                break;
        }

        return $result;
    }

    /**
     * 推流
     */
    protected function handleStreamBegin()
    {
        $streamId = $this->request->getPost('stream_id', 'string');

        $chapter = $this->getChapter($streamId);

        $this->logger->info("Chapter:{$chapter->id} Stream Begin");

        if (!$chapter) return false;

        $attrs = $chapter->attrs;

        $attrs['stream']['status'] = ChapterModel::STREAM_STATUS_ACTIVE;

        $chapter->attrs = $attrs;

        $chapter->update();

        $chapterLive = $this->getChapterLive($chapter->id);

        $chapterLive->status = ChapterLiveModel::STATUS_ACTIVE;

        $chapterLive->update();

        $this->rebuildChapterCache($chapter->id);
        $this->rebuildCatalogCache($chapter->course_id);
        $this->handleStreamBeginNotice($chapter);

        return true;
    }

    /**
     * 断流
     */
    protected function handleStreamEnd()
    {
        $streamId = $this->request->getPost('stream_id', 'string');

        $chapter = $this->getChapter($streamId);

        $this->logger->info("Chapter:{$chapter->id} Stream End");

        if (!$chapter) return false;

        $attrs = $chapter->attrs;

        $attrs['stream']['status'] = ChapterModel::STREAM_STATUS_INACTIVE;

        $chapter->attrs = $attrs;

        $chapter->update();

        $chapterLive = $this->getChapterLive($chapter->id);

        $chapterLive->status = ChapterLiveModel::STATUS_INACTIVE;

        $chapterLive->update();

        $this->rebuildChapterCache($chapter->id);
        $this->rebuildCatalogCache($chapter->course_id);

        return true;
    }

    /**
     * 录制
     */
    protected function handleRecord()
    {
        $streamId = $this->request->getPost('stream_id', 'string');
        $fileId = $this->request->getPost('file_id', 'string');
        $duration = $this->request->getPost('duration', 'int');

        $chapter = $this->getChapter($streamId);

        $this->logger->info("Chapter:{$chapter->id} LiveChat Record Created");

        if (!$chapter) return false;

        $chapterVod = $this->getChapterVod($chapter->id);

        if (!$chapterVod) {

            $chapterVod = new ChapterVodModel();

            $chapterVod->course_id = $chapter->course_id;
            $chapterVod->chapter_id = $chapter->id;
            $chapterVod->file_id = $fileId;

            $chapterVod->create();
        }

        $attrs = $chapter->attrs;

        $attrs['playback']['ready'] = 1;
        $attrs['playback']['duration'] = (int)$duration;

        $chapter->attrs = $attrs;

        $chapter->update();

        $this->rebuildChapterCache($chapter->id);
        $this->rebuildCatalogCache($chapter->id);

        return true;
    }

    /**
     * 截图
     */
    protected function handleSnapshot()
    {
        return true;
    }

    /**
     * 鉴黄
     */
    protected function handlePorn()
    {
        return true;
    }

    protected function handleStreamBeginNotice(ChapterModel $chapter)
    {
        /**
         * 防止发送多次通知
         */
        $cache = $this->getCache();

        $keyName = "live_notify:{$chapter->id}";

        if ($cache->get($keyName)) return;

        $cache->save($keyName, time(), 86400);

        $notice = new LiveBeginNotice();

        $notice->createTask($chapter);
    }

    protected function rebuildChapterCache($chapterId)
    {
        $cache = new ChapterCache();

        $cache->rebuild($chapterId);
    }

    protected function rebuildCatalogCache($courseId)
    {
        $cache = new CourseChapterListCache();

        $cache->rebuild($courseId);
    }

    protected function getChapter($streamName)
    {
        $id = ChapterLiveModel::parseFromStreamName($streamName);

        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findById($id);
    }

    protected function getChapterLive($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterLive($chapterId);
    }

    protected function getChapterVod($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterVod($chapterId);
    }

    /**
     * 检查签名
     *
     * @param string $sign
     * @param int $time
     * @return bool
     */
    protected function checkSign($sign, $time)
    {
        if (!$sign || !$time) return false;

        if ($time < time()) return false;

        $notify = $this->getSettings('live.notify');

        $mySign = md5($notify['auth_key'] . $time);

        return $sign == $mySign;
    }

}
