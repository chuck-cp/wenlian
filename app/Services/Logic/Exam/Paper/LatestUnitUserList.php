<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Caches\ExamPaperLatestUnitUserList;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;

class LatestUnitUserList extends LogicService
{

    use ExamPaperTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaperCache($id);

        $cache = new ExamPaperLatestUnitUserList();

        return $cache->get($paper->id);
    }

}
