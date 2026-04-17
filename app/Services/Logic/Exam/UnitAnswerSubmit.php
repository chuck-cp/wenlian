<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;
use App\Services\Logic\Exam\Question\AnswerScore as AnswerScoreService;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;
use App\Validators\ExamQuestion as ExamQuestionValidator;

class UnitAnswerSubmit extends LogicService
{

    use AnswerSubmitTrait;

    public function handle($id)
    {
        $questionId = $this->request->getPost('question_id', ['trim', 'int']);
        $userAnswer = $this->request->getPost('user_answer', ['trim', 'string']);

        $user = $this->getLoginUser(true);

        $paperUserValidator = new ExamPaperUserValidator();

        $paperUser = $paperUserValidator->checkById($id);

        $paperUserValidator->checkOwner($user->id, $paperUser->user_id);

        $questionValidator = new ExamQuestionValidator();

        $question = $questionValidator->checkExamQuestion($questionId);

        $answerScore = new AnswerScoreService();

        $userScore = $answerScore->getUserScore($question->model, $question->score, $question->answer, $userAnswer);

        $questionUserRepo = new ExamQuestionUserRepo();

        $questionUser = $questionUserRepo->findExamQuestionUser($paperUser->id, $question->id, $user->id);

        try {

            $this->db->begin();

            if (!$questionUser) {

                $questionUser = new ExamQuestionUserModel();

                $questionUser->paper_user_id = $paperUser->id;
                $questionUser->paper_id = $paperUser->paper_id;
                $questionUser->question_id = $question->id;
                $questionUser->question_parent_id = $question->parent_id;
                $questionUser->question_duration = $question->duration;
                $questionUser->question_model = $question->model;
                $questionUser->question_score = $question->score;
                $questionUser->user_id = $user->id;
                $questionUser->user_score = $userScore;
                $questionUser->user_answer = $userAnswer;
                $questionUser->finished = 1;

                $questionUser->create();

                /**
                 * 首次答题纳入通过率统计
                 */
                $this->handleQuestionPassCount($questionUser);

            } else {

                $questionUser->finished = 1;

                $questionUser->update();
            }

            if ($question->parent_id > 0) {
                $this->finishParentQuestionUser($questionUser);
            }

            $this->handleQuestionMistake($questionUser);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Submit Unit Answer Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }

        return $userScore;
    }

    protected function finishParentQuestionUser(ExamQuestionUserModel $child)
    {
        if ($child->question_parent_id == 0) return;

        $questionValidator = new ExamQuestionValidator();

        $question = $questionValidator->checkExamQuestion($child->question_parent_id);

        $questionUserRepo = new ExamQuestionUserRepo();

        $parent = $questionUserRepo->findExamQuestionUser($child->paper_user_id, $question->id, $child->user_id);

        if (!$parent) {

            $parent = new ExamQuestionUserModel();

            $parent->paper_user_id = $child->paper_user_id;
            $parent->paper_id = $child->paper_id;
            $parent->question_id = $question->id;
            $parent->question_parent_id = $question->parent_id;
            $parent->question_duration = $question->duration;
            $parent->question_model = $question->model;
            $parent->question_score = $question->score;
            $parent->user_id = $child->user_id;
            $parent->finished = 1;

            $parent->create();

        } else {

            $parent->finished = 1;

            $parent->update();
        }
    }

}
