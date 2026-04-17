<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;

class ExamQuestionUser extends Validator
{

    public function checkExamQuestionUser($paperUserId, $questionId, $userId)
    {
        $repo = new ExamQuestionUserRepo();

        $questionUser = $repo->findExamQuestionUser($paperUserId, $questionId, $userId);

        if (!$questionUser) {
            throw new BadRequestException('exam_question_user.not_found');
        }

        return $questionUser;
    }

    public function checkAnswerFiles($files)
    {
        if (empty($files)) return [];

        if (is_array($files) && count($files) > 4) {
            throw new BadRequestException('exam_question_user.too_many_answer_file');
        }

        return $files;
    }

    public function checkUserScore($score)
    {
        $value = $this->filter->sanitize($score, ['trim', 'int']);

        if ($value < 0 || $value > 30) {
            throw new BadRequestException('exam_question_user.invalid_user_score');
        }

        return $value;
    }

    public function checkIfAllowMark(ExamPaperUserModel $paperUser)
    {
        if ($paperUser->status != ExamPaperUserModel::STATUS_WAITING) {
            throw new BadRequestException('exam_question_user.mark_not_allowed');
        }
    }

}
