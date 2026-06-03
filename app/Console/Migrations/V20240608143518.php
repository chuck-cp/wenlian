<?php
/**
 * @copyright Copyright (c) 2024 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Console\Migrations;

use App\Caches\CourseChapterList as CourseChapterListCache;
use App\Models\Chapter as ChapterModel;
use App\Models\Course as CourseModel;
use App\Repos\Chapter as ChapterRepo;
use App\Repos\Upload as UploadRepo;
use Phalcon\Mvc\Model\Resultset;

class V20240608143518 extends Migration
{

    public function run()
    {
        $this->handleDocChapters();
    }

    protected function handleDocChapters()
    {
        /**
         * @var $chapters Resultset|ChapterModel[]
         */
        $chapters = ChapterModel::query()
            ->where('model = :model:', ['model' => CourseModel::MODEL_DOC])
            ->execute();

        if ($chapters->count() == 0) return;

        $chapterRepo = new ChapterRepo();

        $uploadRepo = new UploadRepo();

        $courseIds = [];

        foreach ($chapters as $chapter) {

            $doc = $chapterRepo->findChapterDoc($chapter->id);

            if (!$doc) continue;

            $upload = $uploadRepo->findById($doc->upload_id);

            if (!$upload) continue;

            $attrs = $chapter->attrs;

            if (isset($attrs['size']) && $attrs['size'] > 0) continue;

            $attrs['size'] = $upload->size;
            $attrs['format'] = 'html';

            $chapter->attrs = $attrs;

            $chapter->update();

            $courseIds[] = $chapter->course_id;
        }

        $cache = new CourseChapterListCache();

        if (count($courseIds) > 0) {
            foreach ($courseIds as $courseId) {
                $cache->rebuild($courseId);
            }
        }
    }

}