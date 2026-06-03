<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaper as ExamPaperModel;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;
use App\Validators\ExamQuestion as ExamQuestionValidator;
use App\Validators\ExamQuestionUser as ExamQuestionUserValidator;

class MockAnswerMark extends LogicService
{

    public function handle($id)
    {
        $questionId = $this->request->getPost('question_id', ['trim', 'int']);
        $userScore = $this->request->getPost('user_score', ['trim', 'int']);
        $authCode = $this->request->getPost('auth_code', ['trim', 'string']);

        $paperUserValidator = new ExamPaperUserValidator();

        $paperUser = $paperUserValidator->checkById($id);

        $paperUserValidator->checkIfManualGrade($paperUser->grade_type);

        if ($paperUser->grade_type == ExamPaperModel::GRADE_TYPE_TEACHER) {

            $paperUserValidator->checkAuthCode($paperUser->id, $authCode);

        } elseif ($paperUser->grade_type == ExamPaperModel::GRADE_TYPE_STUDENT) {

            $user = $this->getLoginUser();

            $paperUserValidator->checkOwner($user->id, $paperUser->user_id);
        }

        $questionValidator = new ExamQuestionValidator();

        $question = $questionValidator->checkExamQuestion($questionId);

        $questionUserValidator = new ExamQuestionUserValidator();

        $questionUserValidator->checkIfAllowMark($paperUser);

        $userScore = $questionUserValidator->checkUserScore($userScore);

        $questionUser = $questionUserValidator->checkExamQuestionUser($paperUser->id, $question->id, $paperUser->user_id);

        $questionUser->user_score = $userScore;

        $questionUser->update();

        return $questionUser;
    }

}
