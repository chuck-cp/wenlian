<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use App\Validators\ExamPaper as ExamPaperValidator;

trait ExamPaperTrait
{

    public function checkExamPaper($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaper($id);
    }

    public function checkExamPaperCache($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaperCache($id);
    }

}
