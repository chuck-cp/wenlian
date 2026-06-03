<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Services\EditorStorage as EditorStorageService;

class ExamQuestion extends Validator
{

    public function checkExamQuestion($id)
    {
        $questionRepo = new ExamQuestionRepo();

        $question = $questionRepo->findById($id);

        if (!$question) {
            throw new BadRequestException('exam_question.not_found');
        }

        return $question;
    }

    public function checkParent($id)
    {
        $question = $this->checkExamQuestion($id);

        if ($question->model != ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
            throw new BadRequestException('exam_question.invalid_parent');
        }

        return $question;
    }

    public function checkModel($model)
    {
        $list = ExamQuestionModel::modelTypes();

        if (!array_key_exists($model, $list)) {
            throw new BadRequestException('exam_question.invalid_model');
        }

        return $model;
    }

    public function checkLevel($level)
    {
        $list = ExamQuestionModel::levelTypes();

        if (!array_key_exists($level, $list)) {
            throw new BadRequestException('exam_question.invalid_level');
        }

        return $level;
    }

    public function checkTopic($topic)
    {
        $value = $this->filter->sanitize($topic, ['trim']);

        $storage = new EditorStorageService();

        $value = $storage->handle($value);

        $length = kg_editor_content_length($value);

        if ($length < 1) {
            throw new BadRequestException('exam_question.topic_required');
        }

        return kg_clean_html($value);
    }

    public function checkCategoryId($id)
    {
        $result = 0;

        if ($id > 0) {
            $validator = new Category();
            $category = $validator->checkCategory($id);
            $result = $category->id;
        }

        return $result;
    }

    public function checkScore($score)
    {
        $value = $this->filter->sanitize($score, ['trim', 'int']);

        if ($value < 1 || $value > 30) {
            throw new BadRequestException('exam_question.invalid_score');
        }

        return $value;
    }

    public function checkPriority($priority)
    {
        $value = $this->filter->sanitize($priority, ['trim', 'int']);

        if ($value < 1 || $value > 255) {
            throw new BadRequestException('exam_question.invalid_priority');
        }

        return $value;
    }

    public function checkAnswerChoices($choices)
    {
        $storage = new EditorStorageService();

        $result = [];

        /**
         * 过滤掉空值选项
         */
        foreach ($choices as $key => $value) {
            $value = $this->filter->sanitize($value, ['trim']);
            $value = $storage->handle($value);
            if (kg_editor_content_length($value) > 0) {
                $result[$key] = kg_clean_html($value);
            }
        }

        if (count($result) < 2) {
            throw new BadRequestException('exam_question.answer_choice_not_enough');
        }

        return $result;
    }

    public function checkSingleChoiceAnswer($answer)
    {
        $value = $this->filter->sanitize($answer, ['trim', 'string']);

        if (!in_array($value, ['A', 'B', 'C', 'D'])) {
            throw new BadRequestException('exam_question.invalid_answer');
        }

        return $value;
    }

    public function checkMultipleChoiceAnswer($answers)
    {
        if (count($answers) < 1) {
            throw new BadRequestException('exam_question.answer_required');
        }

        foreach ($answers as $answer) {
            if (!in_array($answer, ['A', 'B', 'C', 'D'])) {
                throw new BadRequestException('exam_question.invalid_answer');
            }
        }

        sort($answers);

        return implode(',', $answers);
    }

    public function checkTrueFalseAnswer($answer)
    {
        $value = $this->filter->sanitize($answer, ['trim', 'string']);

        if (!in_array($value, ['T', 'F'])) {
            throw new BadRequestException('exam_question.invalid_answer');
        }

        return $value;
    }

    public function checkBlankFillAnswer($answers)
    {
        if (count($answers) < 1) {
            throw new BadRequestException('exam_question.answer_required');
        }

        foreach ($answers as $answer) {
            $answer = $this->filter->sanitize($answer, ['trim', 'string']);
            if (kg_strlen($answer) < 1) {
                throw new BadRequestException('exam_question.invalid_answer');
            }
        }

        return implode(',', $answers);
    }

    public function checkShortAnswer($answer)
    {
        $value = $this->filter->sanitize($answer, ['trim']);

        $storage = new EditorStorageService();

        $value = $storage->handle($value);

        $length = kg_editor_content_length($value);

        if ($length < 1) {
            throw new BadRequestException('exam_question.answer_required');
        }

        return kg_clean_html($value);
    }

    public function checkSolution($solution)
    {
        $value = $this->filter->sanitize($solution, ['trim']);

        $storage = new EditorStorageService();

        $value = $storage->handle($value);

        return kg_clean_html($value);
    }

    public function checkFeatureStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('exam_question.invalid_feature_status');
        }

        return $status;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('exam_question.invalid_publish_status');
        }

        return $status;
    }

}
