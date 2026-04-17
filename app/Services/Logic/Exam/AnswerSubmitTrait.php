<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\ExamQuestionMistake as ExamQuestionMistakeModel;
use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Repos\ExamQuestionMistake as ExamQuestionMistakeRepo;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;

trait AnswerSubmitTrait
{

    protected function handleQuestionMistake(ExamQuestionUserModel $questionUser)
    {
        /**
         * 非错题不纳入错题本
         */
        if ($questionUser->user_score >= $questionUser->question_score) {
            return;
        }

        /**
         * 子题不纳入错题本
         */
        if ($questionUser->question_parent_id > 0) {
            return;
        }

        /**
         * 题帽题不纳入错题本
         */
        if ($questionUser->question_model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
            return;
        }

        /**
         * 主观题不纳入错题本
         */
        if ($questionUser->question_model == ExamQuestionModel::MODEL_SHORT_ANSWER) {
            return;
        }

        $mistakeRepo = new ExamQuestionMistakeRepo();

        $mistake = $mistakeRepo->findExamQuestionMistake($questionUser->question_id, $questionUser->user_id);

        if (!$mistake) {
            $questionMistake = new ExamQuestionMistakeModel();
            $questionMistake->question_id = $questionUser->question_id;
            $questionMistake->user_id = $questionUser->user_id;
            $questionMistake->create();
        } else {
            if ($mistake->deleted == 1) {
                $mistake->deleted = 0;
                $mistake->update();
            };
        }
    }

    protected function handleQuestionPassCount(ExamQuestionUserModel $questionUser)
    {
        /**
         * 题帽题不参与统计
         */
        if ($questionUser->question_model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
            return;
        }

        /**
         * 主观题不参与统计
         */
        if ($questionUser->question_model == ExamQuestionModel::MODEL_SHORT_ANSWER) {
            return;
        }

        $questionUserRepo = new ExamQuestionUserRepo();

        $record = $questionUserRepo->findFinishedQuestionUser($questionUser->question_id, $questionUser->user_id);

        /**
         * 首次答题才纳入统计
         */
        if (!$record || $questionUser->id != $record->id) {
            return;
        }

        $questionRepo = new ExamQuestionRepo();

        $question = $questionRepo->findById($questionUser->question_id);

        $question->join_count += 1;

        if ($questionUser->user_score >= $questionUser->question_score) {
            $question->pass_count += 1;
        }

        $question->update();
    }

}