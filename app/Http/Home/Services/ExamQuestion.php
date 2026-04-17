<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Services;

use App\Models\ExamQuestion as ExamQuestionModel;
use App\Services\Logic\ExamQuestionTrait;
use App\Services\Logic\Service as LogicService;

class ExamQuestion extends LogicService
{

    use ExamQuestionTrait;

    public function getModelTypes()
    {
        return ExamQuestionModel::modelTypes();
    }

}
