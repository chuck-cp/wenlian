<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Console\Migrations;

use App\Console\Tasks\ExamPaperIndexTask;
use App\Http\Admin\Services\Upload as AdminUploadService;

class V20230513190136 extends Migration
{

    public function run()
    {
        $this->buildExamPaperIndex();
        $this->uploadDefaultArticleCover();
    }

    protected function buildExamPaperIndex()
    {
        $sourceFile = config_path('xs.exam-paper.default.ini');
        $targetFile = config_path('xs.exam-paper.ini');

        if (!file_exists($sourceFile)) return;

        if (!file_exists($targetFile)) {
            copy($sourceFile, $targetFile);
        }

        $index = new ExamPaperIndexTask();

        $index->rebuildAction();
    }

    protected function uploadDefaultArticleCover()
    {
        $settings = $this->getSettings('cos');

        if (empty($settings['bucket'])) return;

        if ($settings['bucket'] == 'course-1255691183') return;

        $service = new AdminUploadService();

        $service->uploadDefaultArticleCover();
    }

}
