<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Question;

use App\Models\ExamQuestion as ExamQuestionModel;
use App\Services\Logic\Service as LogicService;

class AnswerScore extends LogicService
{

    public function getUserScore($questionModel, $questionScore, $orgAnswer, $myAnswer)
    {
        $score = 0;

        $orgAnswer = strtolower($orgAnswer);
        $myAnswer = strtolower($myAnswer);

        if (empty($orgAnswer) || empty($myAnswer)) {
            return $score;
        }

        if ($questionModel == ExamQuestionModel::MODEL_SINGLE_CHOICE) {

            $score = $this->getSingleChoiceScore($questionScore, $orgAnswer, $myAnswer);

        } elseif ($questionModel == ExamQuestionModel::MODEL_MULTIPLE_CHOICE) {

            $score = $this->getMultipleChoiceScore($questionScore, $orgAnswer, $myAnswer);

        } elseif ($questionModel == ExamQuestionModel::MODEL_TRUE_FALSE) {

            $score = $this->getSingleChoiceScore($questionScore, $orgAnswer, $myAnswer);

        } elseif ($questionModel == ExamQuestionModel::MODEL_BLANK_FILL) {

            $score = $this->getBlankFillScore($questionScore, $orgAnswer, $myAnswer);
        }

        return $score;
    }

    protected function getSingleChoiceScore($questionScore, $orgAnswer, $myAnswer)
    {
        $score = 0;

        if ($orgAnswer == $myAnswer) {
            $score = $questionScore;
        }

        return $score;
    }

    protected function getMultipleChoiceScore($questionScore, $orgAnswer, $myAnswer)
    {
        $score = 0;

        /**
         * 兼容没有分隔符的情况
         */
        if (strpos($orgAnswer, ',') !== false) {
            $orgAnswers = explode(',', $orgAnswer);
        } else {
            $orgAnswers = str_split($orgAnswer);
        }

        $orgAnswers = array_unique($orgAnswers);

        /**
         * 兼容没有分隔符的情况
         */
        if (strpos($myAnswer, ',') !== false) {
            $myAnswers = explode(',', $myAnswer);
        } else {
            $myAnswers = str_split($myAnswer);
        }

        $myAnswers = array_unique($myAnswers);

        $diff = array_diff($myAnswers, $orgAnswers);

        // 和答案出现差异选项，视为错误
        if (count($diff) > 0) return $score;

        // 全部匹配给满分，漏选不得分
        if (count($orgAnswers) == count($myAnswers)) {
            $score = $questionScore;
        }

        return $score;
    }

    protected function getBlankFillScore($questionScore, $orgAnswer, $myAnswer)
    {
        $orgAnswers = explode(',', $orgAnswer);
        $myAnswers = explode(',', $myAnswer);

        $answerCount = count($orgAnswers);

        $unitScore = floor($questionScore / $answerCount);

        $hitCount = 0;

        foreach ($orgAnswers as $key => $value) {
            if (isset($myAnswers[$key]) && $myAnswers[$key] == $value) {
                $hitCount++;
            }
        }

        if ($answerCount == $hitCount) {
            $score = $questionScore;
        } else {
            $score = $hitCount * $unitScore;
        }

        return $score;
    }

}
