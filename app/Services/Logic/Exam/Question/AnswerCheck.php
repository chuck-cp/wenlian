<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Question;

use App\Services\Logic\Service as LogicService;
use App\Validators\ExamQuestion as ExamQuestionValidator;

class AnswerCheck extends LogicService
{

    public function handle($id)
    {
        $userAnswer = $this->request->getPost('user_answer', ['trim', 'string']);

        $questionValidator = new ExamQuestionValidator();

        $question = $questionValidator->checkExamQuestion($id);

        $answerScore = new AnswerScore();

        return $answerScore->getUserScore($question->model, $question->score, $question->answer, $userAnswer);
    }

}
