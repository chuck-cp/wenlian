<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Services\Logic\Exam\Question\AnswerScore as AnswerScoreService;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;
use App\Validators\ExamQuestion as ExamQuestionValidator;
use App\Validators\ExamQuestionUser as ExamQuestionUserValidator;

class MockAnswerSubmit extends LogicService
{

    public function handle($id)
    {
        $questionId = $this->request->getPost('question_id', ['trim', 'int']);
        $userAnswer = $this->request->getPost('user_answer', ['trim', 'string']);
        $userAnswerFiles = $this->request->getPost('user_answer_files', null, []);

        $user = $this->getLoginUser(true);

        $paperUserValidator = new ExamPaperUserValidator();

        $paperUser = $paperUserValidator->checkById($id);

        $paperUserValidator->checkIfActiveMock($paperUser);

        $paperUserValidator->checkOwner($user->id, $paperUser->user_id);

        $questionValidator = new ExamQuestionValidator();

        $question = $questionValidator->checkExamQuestion($questionId);

        $questionUserValidator = new ExamQuestionUserValidator();

        $userAnswerFiles = $questionUserValidator->checkAnswerFiles($userAnswerFiles);

        $questionUser = $questionUserValidator->checkExamQuestionUser($paperUser->id, $question->id, $user->id);

        $answerScore = new AnswerScoreService();

        $userScore = $answerScore->getUserScore($question->model, $question->score, $question->answer, $userAnswer);

        $questionUser->user_answer_files = $userAnswerFiles;
        $questionUser->user_answer = $userAnswer;
        $questionUser->user_score = $userScore;
        $questionUser->finished = 1;

        $questionUser->update();

        return $questionUser;
    }

}
