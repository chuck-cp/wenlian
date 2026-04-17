<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

class ExamPaperQuestion extends Validator
{

    public function checkExamPaper($id)
    {
        $validator = new ExamPaper();

        return $validator->checkExamPaper($id);
    }

    public function checkExamQuestion($id)
    {
        $validator = new ExamQuestion();

        return $validator->checkExamQuestion($id);
    }

}
