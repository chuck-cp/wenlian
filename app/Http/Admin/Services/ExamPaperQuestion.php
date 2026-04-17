<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperQuestion as ExamPaperQuestionModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamPaperQuestion as ExamPaperQuestionRepo;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Services\Category as CategoryService;
use App\Validators\ExamPaper as ExamPaperValidator;
use App\Validators\ExamPaperQuestion as ExamPaperQuestionValidator;

class ExamPaperQuestion extends Service
{

    public function createPaperQuestion()
    {
        $paperId = $this->request->getPost('paper_id', ['trim', 'int']);
        $questionIds = $this->request->getPost('question_ids', ['trim', 'string']);

        if (empty($questionIds)) return;

        $validator = new ExamPaperQuestionValidator();

        $paper = $validator->checkExamPaper($paperId);

        $questionIds = explode(',', $questionIds);

        $questionRepo = new ExamQuestionRepo();

        $questions = $questionRepo->findByIds($questionIds);

        if ($questions->count() == 0) return;

        $relationRepo = new ExamPaperQuestionRepo();

        foreach ($questions as $question) {

            $paperQuestion = $relationRepo->findPaperQuestion($paper->id, $question->id);

            if ($paperQuestion) {
                if ($paperQuestion->deleted == 1) {
                    $paperQuestion->deleted = 0;
                    $paperQuestion->update();
                }
            } else {
                $paperQuestion = new ExamPaperQuestionModel();
                $paperQuestion->paper_id = $paper->id;
                $paperQuestion->question_id = $question->id;
                $paperQuestion->question_model = $question->model;
                $paperQuestion->question_score = $question->score;
                $paperQuestion->question_parent_id = $question->parent_id;
                $paperQuestion->create();
            }
        }

        $this->syncPaperStats($paper);
    }

    public function deletePaperQuestion()
    {
        $paperId = $this->request->getPost('paper_id', ['trim', 'int']);
        $questionId = $this->request->getPost('question_id', ['trim', 'int']);

        $validator = new ExamPaperQuestionValidator();

        $paper = $validator->checkExamPaper($paperId);
        $question = $validator->checkExamQuestion($questionId);

        $relationRepo = new ExamPaperQuestionRepo();

        $paperQuestion = $relationRepo->findPaperQuestion($paper->id, $question->id);

        if (!$paperQuestion) return;

        if ($paperQuestion->deleted == 0) {
            $paperQuestion->deleted = 1;
            $paperQuestion->update();
        }

        $this->syncPaperStats($paper);
    }

    public function packByRandom()
    {
        $post = $this->request->getPost();

        $singleChoice = [
            'model' => ExamQuestionModel::MODEL_SINGLE_CHOICE,
            'limit' => $post['single_choice']['limit'] ?? 0,
            'level' => $post['single_choice']['level'] ?? [],
        ];

        $multipleChoice = [
            'model' => ExamQuestionModel::MODEL_MULTIPLE_CHOICE,
            'limit' => $post['multiple_choice']['limit'] ?? 0,
            'level' => $post['multiple_choice']['level'] ?? [],
        ];

        $trueFalse = [
            'model' => ExamQuestionModel::MODEL_TRUE_FALSE,
            'limit' => $post['true_false']['limit'] ?? 0,
            'level' => $post['true_false']['level'] ?? [],
        ];

        $blankFill = [
            'model' => ExamQuestionModel::MODEL_BLANK_FILL,
            'limit' => $post['blank_fill']['limit'] ?? 0,
            'level' => $post['blank_fill']['level'] ?? [],
        ];

        $shortAnswer = [
            'model' => ExamQuestionModel::MODEL_SHORT_ANSWER,
            'limit' => $post['short_answer']['limit'] ?? 0,
            'level' => $post['short_answer']['level'] ?? [],
        ];

        $complexQuestion = [
            'model' => ExamQuestionModel::MODEL_COMPLEX_QUESTION,
            'limit' => $post['complex_question']['limit'] ?? 0,
            'level' => $post['complex_question']['level'] ?? [],
        ];

        $paperValidator = new ExamPaperValidator();

        $paper = $paperValidator->checkExamPaper($post['paper_id']);

        $categoryIds = $this->getChildCategoryIds($post['xm_category_ids']);

        $default = [
            'limit' => 0,
            'level' => 0,
            'meet' => 1,
        ];

        $result = [
            'meet_all' => 1,
            'single_choice' => $default,
            'multiple_choice' => $default,
            'true_false' => $default,
            'blank_fill' => $default,
            'short_answer' => $default,
            'complex_question' => $default,
        ];

        $conditions = [];

        if ($singleChoice['limit'] > 0) {
            $conditions['single_choice'] = $singleChoice;
            $result['single_choice'] = $this->previewRandomizer($categoryIds, $singleChoice['model'], $singleChoice['level'], $singleChoice['limit']);
            if ($result['single_choice']['meet'] == 0) {
                $result['meet_all'] = 0;
            }
        }

        if ($multipleChoice['limit'] > 0) {
            $conditions['multiple_choice'] = $multipleChoice;
            $result['multiple_choice'] = $this->previewRandomizer($categoryIds, $multipleChoice['model'], $multipleChoice['level'], $multipleChoice['limit']);
            if ($result['multiple_choice']['meet'] == 0) {
                $result['meet_all'] = 0;
            }
        }

        if ($trueFalse['limit'] > 0) {
            $conditions['true_false'] = $trueFalse;
            $result['true_false'] = $this->previewRandomizer($categoryIds, $trueFalse['model'], $trueFalse['level'], $trueFalse['limit']);
            if ($result['true_false']['meet'] == 0) {
                $result['meet_all'] = 0;
            }
        }

        if ($blankFill['limit'] > 0) {
            $conditions['blank_fill'] = $blankFill;
            $result['blank_fill'] = $this->previewRandomizer($categoryIds, $blankFill['model'], $blankFill['level'], $blankFill['limit']);
            if ($result['blank_fill']['meet'] == 0) {
                $result['meet_all'] = 0;
            }
        }

        if ($shortAnswer['limit'] > 0) {
            $conditions['short_answer'] = $shortAnswer;
            $result['short_answer'] = $this->previewRandomizer($categoryIds, $shortAnswer['model'], $shortAnswer['level'], $shortAnswer['limit']);
            if ($result['short_answer']['meet'] == 0) {
                $result['meet_all'] = 0;
            }
        }

        if ($complexQuestion['limit'] > 0) {
            $conditions['complex_question'] = $complexQuestion;
            $result['complex_question'] = $this->previewRandomizer($categoryIds, $complexQuestion['model'], $complexQuestion['level'], $complexQuestion['limit']);
            if ($result['complex_question']['meet'] == 0) {
                $result['meet_all'] = 0;
            }
        }

        if (count($conditions) == 0) {
            return $result;
        }

        if ($result['meet_all'] == 0) {
            return $result;
        }

        /**
         * １．模拟考试不是刷题，随机组卷题量是固定的，
         * ２．同步练习用于刷题，尽可能按照组卷模板随机穷举题目
         */
        if ($paper->exam_type == ExamPaperModel::EXAM_TYPE_MOCK) {
            $questionCount = 0;
            foreach ($conditions as $condition) {
                $questionCount += $condition['limit'];
            }
            $paper->question_count = $questionCount;
        }

        $attrs = $paper->attrs;
        $attrs['category_ids'] = $categoryIds;
        $attrs['conditions'] = $conditions;
        $paper->attrs = $attrs;

        $paper->update();

        return $result;
    }

    protected function previewRandomizer($categoryId, $model, $level, $limit)
    {
        $result = [
            'model' => $model,
            'limit' => $limit,
            'level' => $level,
        ];

        $where = [
            'published' => 1,
            'deleted' => 0,
            'parent_id' => 0,
            'category_id' => $categoryId,
            'model' => $model,
            'level' => $level,
        ];

        $questionRepo = new ExamQuestionRepo();

        $questions = $questionRepo->findByRand($where, $limit);

        $meetCount = $questions->count();

        $result['meet'] = $meetCount >= $limit ? 1 : 0;

        return $result;
    }

    protected function syncPaperStats(ExamPaperModel $paper)
    {
        $paperRepo = new ExamPaperRepo();

        $questions = $paperRepo->findQuestions($paper->id);

        $questionRepo = new ExamQuestionRepo();

        $totalScore = 0;
        $questionCount = 0;

        foreach ($questions as $question) {
            if ($question->model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
                $childQuestions = $questionRepo->findChildQuestions($question->id);
                foreach ($childQuestions as $childQuestion) {
                    $totalScore += $childQuestion->score;
                    $questionCount += 1;
                }
            } else {
                $totalScore += $question->score;
                $questionCount += 1;
            }
        }

        $paper->total_score = $totalScore;
        $paper->question_count = $questionCount;
        $paper->update();
    }

    protected function getChildCategoryIds($xmCategoryIds)
    {
        if (!$xmCategoryIds) return [];

        $ids = explode(',', $xmCategoryIds);

        $result = [];

        $service = new CategoryService();

        foreach ($ids as $id) {
            $result = array_merge($result, $service->getChildCategoryIds($id));
        }

        return $result;
    }

}
