<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use App\Validators\ExamQuestion as ExamQuestionValidator;

trait ExamQuestionTrait
{

    public function checkExamQuestion($id)
    {
        $validator = new ExamQuestionValidator();

        return $validator->checkExamQuestion($id);
    }

    public function getQuestionPassRate($joinCount, $passCount)
    {
        $passRate = 0.00;

        /**
         * 避免人数过少影响精度
         */
        if ($joinCount > 9) {
            $passRate = round($passCount / $joinCount, 2);
        }

        return min($passRate, 1.00);
    }

}
