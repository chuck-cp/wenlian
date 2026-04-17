<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Chapter;

use App\Models\Chapter as ChapterModel;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Models\ChapterVod as ChapterVodModel;
use App\Models\Course as CourseModel;
use App\Repos\Chapter as ChapterRepo;
use App\Repos\Upload as UploadRepo;
use App\Services\ChapterVod as ChapterVodService;
use App\Services\Live as LiveService;
use App\Services\Logic\ChapterTrait;
use App\Services\Logic\ContentTrait;
use App\Services\Logic\CourseTrait;
use App\Services\Logic\Service as LogicService;
use App\Services\Storage as StorageService;

class BasicInfo extends LogicService
{

    use CourseTrait;
    use ChapterTrait;
    use ContentTrait;

    public function handle($id)
    {
        $chapter = $this->checkChapter($id);

        $course = $this->checkCourse($chapter->course_id);

        $result = $this->handleBasicInfo($chapter);

        $result['course'] = $this->handleCourseInfo($course);

        return $result;
    }

    public function handleBasicInfo(ChapterModel $chapter)
    {
        $result = [];

        switch ($chapter->model) {
            case CourseModel::MODEL_VOD:
                $result = $this->formatChapterVod($chapter);
                break;
            case CourseModel::MODEL_LIVE:
                $result = $this->formatChapterLive($chapter);
                break;
            case CourseModel::MODEL_READ:
                $result = $this->formatChapterRead($chapter);
                break;
            case CourseModel::MODEL_DOC:
                $result = $this->formatChapterDoc($chapter);
                break;
        }

        return $result;
    }

    public function handleCourseInfo(CourseModel $course)
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'cover' => $course->cover,
        ];
    }

    protected function formatChapterVod(ChapterModel $chapter)
    {
        $chapterRepo = new ChapterRepo();

        $vod = $chapterRepo->findChapterVod($chapter->id);

        $encrypted = !empty($vod->file_encrypt) ? 1 : 0;

        $encryptInfo = $this->getVodEncryptInfo($vod);

        $chapterVodService = new ChapterVodService();

        $playUrls = $chapterVodService->getPlayUrls($vod);

        $settings = $this->getVodSettings($vod);

        return [
            'id' => $chapter->id,
            'title' => $chapter->title,
            'summary' => $chapter->summary,
            'model' => $chapter->model,
            'encrypted' => $encrypted,
            'published' => $chapter->published,
            'deleted' => $chapter->deleted,
            'encrypt_info' => $encryptInfo,
            'play_urls' => $playUrls,
            'settings' => $settings,
            'comment_count' => $chapter->comment_count,
            'user_count' => $chapter->user_count,
            'like_count' => $chapter->like_count,
            'create_time' => $chapter->create_time,
            'update_time' => $chapter->update_time,
        ];
    }

    protected function formatChapterLive(ChapterModel $chapter)
    {
        $liveService = new LiveService();

        $streamName = ChapterLiveModel::generateStreamName($chapter->id);

        $playUrls = $liveService->getPullUrls($streamName);

        $chapterRepo = new ChapterRepo();

        $live = $chapterRepo->findChapterLive($chapter->id);

        $settings = $this->getLiveSettings($live);

        $playback = $this->getLivePlaybackInfo($chapter);

        return [
            'id' => $chapter->id,
            'title' => $chapter->title,
            'summary' => $chapter->summary,
            'model' => $chapter->model,
            'published' => $chapter->published,
            'deleted' => $chapter->deleted,
            'play_urls' => $playUrls,
            'start_time' => $live->start_time,
            'end_time' => $live->end_time,
            'status' => $live->status,
            'settings' => $settings,
            'playback' => $playback,
            'comment_count' => $chapter->comment_count,
            'user_count' => $chapter->user_count,
            'like_count' => $chapter->like_count,
            'create_time' => $chapter->create_time,
            'update_time' => $chapter->update_time,
        ];
    }

    protected function formatChapterRead(ChapterModel $chapter)
    {
        $chapterRepo = new ChapterRepo();

        $read = $chapterRepo->findChapterRead($chapter->id);

        $content = $this->handleContent($read->content);

        return [
            'id' => $chapter->id,
            'title' => $chapter->title,
            'summary' => $chapter->summary,
            'model' => $chapter->model,
            'format' => $read->format,
            'content' => $content,
            'markdown' => $read->markdown,
            'settings' => $read->settings,
            'published' => $chapter->published,
            'deleted' => $chapter->deleted,
            'comment_count' => $chapter->comment_count,
            'user_count' => $chapter->user_count,
            'like_count' => $chapter->like_count,
            'create_time' => $chapter->create_time,
            'update_time' => $chapter->update_time,
        ];
    }

    protected function formatChapterDoc(ChapterModel $chapter)
    {
        $chapterRepo = new ChapterRepo();

        $doc = $chapterRepo->findChapterDoc($chapter->id);

        $previewUrl = '';

        if ($doc->upload_id > 0) {
            $uploadRepo = new UploadRepo();
            $upload = $uploadRepo->findById($doc->upload_id);

            $storage = new StorageService();
            $previewUrl = $storage->getDocPreviewUrl($upload->path);
        }

        return [
            'id' => $chapter->id,
            'title' => $chapter->title,
            'summary' => $chapter->summary,
            'model' => $chapter->model,
            'preview_url' => $previewUrl,
            'settings' => $doc->settings,
            'published' => $chapter->published,
            'deleted' => $chapter->deleted,
            'comment_count' => $chapter->comment_count,
            'user_count' => $chapter->user_count,
            'like_count' => $chapter->like_count,
            'create_time' => $chapter->create_time,
            'update_time' => $chapter->update_time,
        ];
    }

    protected function getVodSettings(ChapterVodModel $vod)
    {
        /**
         * 重新执行获取后置操作，前面可能有写入操作
         */
        $vod->afterFetch();

        $vodSettings = kg_setting('vod');

        $settings = $vod->settings;

        $settings['fast_forward_enabled'] = $vodSettings['fast_forward_enabled'];
        $settings['human_verify_enabled'] = $vodSettings['human_verify_enabled'];
        $settings['switch_anti_enabled'] = $vodSettings['switch_anti_enabled'];
        $settings['record_anti_enabled'] = $vodSettings['record_anti_enabled'];
        $settings['record_anti_config'] = json_decode($vodSettings['record_anti_config'], true);

        return $settings;
    }

    protected function getVodEncryptInfo(ChapterVodModel $vod)
    {
        if (empty($vod->file_encrypt)) return new \stdClass();

        $settings = $this->getSettings('vod');

        $vodService = new ChapterVodService();

        $sign = $vodService->getTcplayerSignature($vod);

        return [
            'app_id' => $settings['sub_app_id'],
            'file_id' => $vod->file_id,
            'sign' => $sign,
        ];
    }

    protected function getLiveSettings(ChapterLiveModel $live)
    {
        /**
         * 重新执行获取后置操作，前面可能有写入操作
         */
        $live->afterFetch();

        $pullSettings = kg_setting('live.pull');

        $settings = $live->settings;

        $settings['webrtc_enabled'] = $pullSettings['webrtc_enabled'];

        return $settings;
    }

    protected function getLivePlaybackInfo(ChapterModel $chapter)
    {
        /**
         * 重新执行获取后置操作，前面可能有写入操作
         */
        $chapter->afterFetch();

        $playback = $chapter->attrs['playback'];

        $chapterRepo = new ChapterRepo();

        $vod = $chapterRepo->findChapterVod($chapter->id);

        if (!$vod) return $playback;

        $encrypted = !empty($vod->file_encrypt) ? 1 : 0;

        $encryptInfo = $this->getVodEncryptInfo($vod);

        $chapterVodService = new ChapterVodService();

        $playUrls = $chapterVodService->getPlayUrls($vod);

        $playback['encrypted'] = $encrypted;
        $playback['encrypt_info'] = $encryptInfo;
        $playback['play_urls'] = $playUrls;

        return $playback;
    }

}
