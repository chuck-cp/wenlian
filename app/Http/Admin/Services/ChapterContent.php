<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Caches\CourseChapterList as CatalogCache;
use App\Library\Utils\Word as WordUtil;
use App\Models\Chapter as ChapterModel;
use App\Models\Course as CourseModel;
use App\Models\Upload as UploadModel;
use App\Repos\Chapter as ChapterRepo;
use App\Repos\Upload as UploadRepo;
use App\Services\ChapterVod as ChapterVodService;
use App\Services\CourseStat as CourseStatService;
use App\Services\Vod as VodService;
use App\Validators\Article as ArticleValidator;
use App\Validators\ChapterDoc as ChapterDocValidator;
use App\Validators\ChapterLive as ChapterLiveValidator;
use App\Validators\ChapterOffline as ChapterOfflineValidator;
use App\Validators\ChapterVod as ChapterVodValidator;

class ChapterContent extends Service
{

    public function getChapterVod($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterVod($chapterId);
    }

    public function getChapterLive($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterLive($chapterId);
    }

    public function getChapterRead($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterRead($chapterId);
    }

    public function getChapterOffline($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterOffline($chapterId);
    }

    public function getChapterDoc($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterDoc($chapterId);
    }

    public function getDocUpload($chapterId)
    {
        $doc = $this->getChapterDoc($chapterId);

        if ($doc->upload_id > 0) {
            $uploadRepo = new UploadRepo();
            $upload = $uploadRepo->findById($doc->upload_id);
        } else {
            $upload = new UploadModel();
        }

        return $upload;
    }

    public function getChapterDuration($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        $chapter = $chapterRepo->findById($chapterId);

        $duration = $chapter->attrs['duration'] ?? 0;

        $result = ['hours' => 0, 'minutes' => 0, 'seconds' => 0];

        if ($duration == 0) return $result;

        $result['hours'] = floor($duration / 3600);
        $result['minutes'] = floor(($duration - $result['hours'] * 3600) / 60);
        $result['seconds'] = $duration % 60;

        return $result;
    }

    public function updateChapterContent($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        $chapter = $chapterRepo->findById($chapterId);

        switch ($chapter->model) {
            case CourseModel::MODEL_VOD:
                $this->updateChapterVod($chapter);
                break;
            case CourseModel::MODEL_LIVE:
                $this->updateChapterLive($chapter);
                break;
            case CourseModel::MODEL_READ:
                $this->updateChapterRead($chapter);
                break;
            case CourseModel::MODEL_OFFLINE:
                $this->updateChapterOffline($chapter);
                break;
            case CourseModel::MODEL_DOC:
                $this->updateChapterDoc($chapter);
                break;
        }

        $this->rebuildCatalogCache($chapter->course_id);
    }

    public function transcode($chapterId)
    {
        $mode = $this->request->getPost('mode');

        $validator = new ChapterVodValidator();

        $mode = $validator->checkTransMode($mode);

        $chapterRepo = new ChapterRepo();

        $chapter = $chapterRepo->findById($chapterId);

        $vod = $chapterRepo->findChapterVod($chapterId);

        $vodService = new VodService();

        $attrs = $chapter->attrs;

        if ($mode == ChapterModel::TRANS_MODE_STANDARD) {
            if ($attrs['transcode']['standard']['status'] != ChapterModel::TRANS_STATUS_PROCESSING) {
                $vodService->createTransVideoTask($vod->file_id);
                $attrs['transcode']['standard']['status'] = ChapterModel::TRANS_STATUS_PROCESSING;
            }
        } elseif ($mode == ChapterModel::TRANS_MODE_ENCRYPT) {
            if ($attrs['transcode']['encrypt']['status'] != ChapterModel::TRANS_STATUS_PROCESSING) {
                $vodService->createEncryptVideoTask($vod->file_id);
                $attrs['transcode']['encrypt']['status'] = ChapterModel::TRANS_STATUS_PROCESSING;
            }
        }

        $chapter->attrs = $attrs;

        $chapter->update();
    }

    protected function updateChapterVod(ChapterModel $chapter)
    {
        $section = $this->request->getPost('section', 'string', 'settings');

        if ($section == 'settings') {
            $this->updateChapterVodSettings($chapter);
        } elseif ($section == 'cos') {
            $this->updateChapterVodCos($chapter);
        } elseif ($section == 'remote') {
            $this->updateChapterVodRemote($chapter);
        }
    }

    protected function updateChapterVodSettings(ChapterModel $chapter)
    {
        $post = $this->request->getPost();

        $chapterRepo = new ChapterRepo();

        $vod = $chapterRepo->findChapterVod($chapter->id);

        $settings = $vod->settings;

        if (isset($post['settings'])) {
            $settings['comment_enabled'] = $post['settings']['comment_enabled'] ?? 1;
            $settings['speed_enabled'] = $post['settings']['speed_enabled'] ?? 1;
            $settings['danmu_enabled'] = $post['settings']['danmu_enabled'] ?? 1;
            $settings['verify_enabled'] = $post['settings']['verify_enabled'] ?? 1;
            $vod->settings = $settings;
        }

        $vod->update();
    }

    protected function updateChapterVodCos(ChapterModel $chapter)
    {
        $fileId = $this->request->getPost('file_id', 'string', 0);
        $transMode = $this->request->getPost('trans_mode', 'string', ChapterModel::TRANS_MODE_NONE);

        $chapterRepo = new ChapterRepo();

        $vod = $chapterRepo->findChapterVod($chapter->id);

        $validator = new ChapterVodValidator();

        $fileId = $validator->checkFileId($fileId);
        $transMode = $validator->checkTransMode($transMode);

        $attrs = $chapter->attrs;

        if ($fileId != $vod->file_id) {
            $vod->file_id = $fileId;
            $vod->file_origin = [];
            $vod->file_encrypt = [];
            $vod->file_transcode = [];
            $vod->update();

            $attrs['transcode']['standard']['status'] = ChapterModel::TRANS_STATUS_PENDING;
            $attrs['transcode']['encrypt']['status'] = ChapterModel::TRANS_STATUS_PENDING;
            $attrs['duration'] = 0;
        }

        if ($transMode == ChapterModel::TRANS_MODE_STANDARD) {
            $attrs['transcode']['standard']['status'] = ChapterModel::TRANS_STATUS_CREATED;
        } elseif ($transMode == ChapterModel::TRANS_MODE_ENCRYPT) {
            $attrs['transcode']['encrypt']['status'] = ChapterModel::TRANS_STATUS_CREATED;
        }

        $chapter->attrs = $attrs;

        $chapter->update();

        $this->pullMediaInfo($vod->chapter_id);

        $this->updateCourseAttrs($vod->course_id);
    }

    protected function updateChapterVodRemote(ChapterModel $chapter)
    {
        $post = $this->request->getPost();

        $validator = new ChapterVodValidator();

        $hours = $post['file_remote']['duration']['hours'] ?? 0;
        $minutes = $post['file_remote']['duration']['minutes'] ?? 0;
        $seconds = $post['file_remote']['duration']['seconds'] ?? 0;

        $duration = 3600 * $hours + 60 * $minutes + $seconds;

        $validator->checkDuration($duration);

        $hdUrl = $post['file_remote']['hd']['url'] ?? '';
        $sdUrl = $post['file_remote']['sd']['url'] ?? '';
        $fdUrl = $post['file_remote']['fd']['url'] ?? '';

        $fileRemote = [
            'hd' => ['url' => ''],
            'sd' => ['url' => ''],
            'fd' => ['url' => ''],
        ];

        if (!empty($hdUrl)) {
            $fileRemote['hd']['url'] = $validator->checkFileUrl($hdUrl);
        }

        if (!empty($sdUrl)) {
            $fileRemote['sd']['url'] = $validator->checkFileUrl($sdUrl);
        }

        if (!empty($fdUrl)) {
            $fileRemote['fd']['url'] = $validator->checkFileUrl($fdUrl);
        }

        $validator->checkRemoteFile($hdUrl, $sdUrl, $fdUrl);

        $chapterRepo = new ChapterRepo();

        $vod = $chapterRepo->findChapterVod($chapter->id);

        $vod->file_remote = $fileRemote;

        $vod->update();

        $attrs = $chapter->attrs;
        $attrs['duration'] = $duration;
        $chapter->attrs = $attrs;

        $chapter->update();

        $this->updateCourseAttrs($vod->course_id);
    }

    protected function updateChapterLive(ChapterModel $chapter)
    {
        $post = $this->request->getPost();

        $chapterRepo = new ChapterRepo();

        $live = $chapterRepo->findChapterLive($chapter->id);

        $validator = new ChapterLiveValidator();

        $startTime = $validator->checkStartTime($post['start_time']);
        $endTime = $validator->checkEndTime($post['end_time']);

        $validator->checkTimeRange($startTime, $endTime);

        $live->start_time = $startTime;
        $live->end_time = $endTime;

        $settings = $live->settings;

        if (isset($post['settings'])) {
            $settings['record_enabled'] = $post['settings']['record_enabled'] ?? 1;
            $settings['chat_enabled'] = $post['settings']['chat_enabled'] ?? 1;
            $settings['comment_enabled'] = $post['settings']['comment_enabled'] ?? 1;
            $settings['danmu_enabled'] = $post['settings']['danmu_enabled'] ?? 1;
            $settings['speed_enabled'] = $post['settings']['speed_enabled'] ?? 1;
            $live->settings = $settings;
        }

        $live->update();

        $attrs = $chapter->attrs;

        $attrs['start_time'] = $startTime;
        $attrs['end_time'] = $endTime;
        $chapter->attrs = $attrs;

        $chapter->update();

        $this->updateCourseAttrs($live->course_id);
    }

    protected function updateChapterRead(ChapterModel $chapter)
    {
        $post = $this->request->getPost();

        $chapterRepo = new ChapterRepo();

        $read = $chapterRepo->findChapterRead($chapter->id);

        $validator = new ArticleValidator();

        $attrs = $chapter->attrs;

        if (isset($post['content'])) {
            if ($attrs['format'] == 'html') {
                $read->content = $validator->checkHtmlContent($post['content']);
            } elseif ($attrs['format'] == 'markdown') {
                $read->markdown = $validator->checkMarkdownContent($post['content']);
                $read->content = kg_parse_markdown($read->markdown);
            }
        }

        $settings = $read->settings;

        if (isset($post['settings'])) {
            $settings['comment_enabled'] = $post['settings']['comment_enabled'] ?? 1;
            $settings['copy_enabled'] = $post['settings']['copy_enabled'] ?? 1;
            $read->settings = $settings;
        }

        $read->update();

        $attrs['word_count'] = WordUtil::getWordCount($read->content);
        $attrs['duration'] = WordUtil::getWordDuration($read->content);
        $chapter->attrs = $attrs;

        $chapter->update();

        $this->updateCourseAttrs($read->course_id);
    }

    protected function updateChapterOffline(ChapterModel $chapter)
    {
        $post = $this->request->getPost();

        $chapterRepo = new ChapterRepo();

        $offline = $chapterRepo->findChapterOffline($chapter->id);

        $validator = new ChapterOfflineValidator();

        $startTime = $validator->checkStartTime($post['start_time']);
        $endTime = $validator->checkEndTime($post['end_time']);

        $validator->checkTimeRange($startTime, $endTime);

        $offline->start_time = $startTime;
        $offline->end_time = $endTime;

        $offline->update();

        $attrs = $chapter->attrs;
        $attrs['start_time'] = $startTime;
        $attrs['end_time'] = $endTime;
        $chapter->attrs = $attrs;

        $chapter->update();

        $this->updateCourseAttrs($offline->course_id);
    }

    protected function updateChapterDoc(ChapterModel $chapter)
    {
        $post = $this->request->getPost();

        $chapterRepo = new ChapterRepo();

        $doc = $chapterRepo->findChapterDoc($chapter->id);

        $validator = new ChapterDocValidator();

        $upload = $validator->checkUpload($post['upload_id']);

        $doc->upload_id = $upload->id;

        $settings = $doc->settings;

        if (isset($post['settings'])) {
            $settings['comment_enabled'] = $post['settings']['comment_enabled'] ?? 1;
            $settings['copy_enabled'] = $post['settings']['copy_enabled'] ?? 1;
            $doc->settings = $settings;
        }

        $doc->update();

        $attrs = $chapter->attrs;
        $attrs['size'] = $upload->size;
        $chapter->attrs = $attrs;

        $chapter->update();

        $this->updateCourseAttrs($doc->course_id);
    }

    protected function updateCourseAttrs($courseId)
    {
        $statService = new CourseStatService();

        $statService->updateAttrs($courseId);
    }

    protected function rebuildCatalogCache($courseId)
    {
        $cache = new CatalogCache();

        $cache->rebuild($courseId);
    }

    protected function pullMediaInfo($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        $chapterVod = $chapterRepo->findChapterVod($chapterId);

        $service = new ChapterVodService();

        $service->pullMediaInfo($chapterVod);
    }

}
