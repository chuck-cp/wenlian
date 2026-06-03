<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamPaper as ExamPaperRepo;

class ExamPaperQuestionStat extends Service
{

    protected $questionMappings = [];

    public function handle($id)
    {
        $this->questionMappings = $this->getQuestionMappings($id);

        return $this->getPaperQuestions($id);
    }

    protected function getPaperQuestions($paperId)
    {
        $paperRepo = new ExamPaperRepo();

        $questions = $paperRepo->findQuestions($paperId);

        if ($questions->count() == 0) return [];

        $questions = $questions->toArray();

        $result = [];

        $models = [
            ExamQuestionModel::MODEL_SINGLE_CHOICE,
            ExamQuestionModel::MODEL_MULTIPLE_CHOICE,
            ExamQuestionModel::MODEL_TRUE_FALSE,
            ExamQuestionModel::MODEL_BLANK_FILL,
        ];

        foreach ($models as $model) {
            $result[] = $this->handleModelQuestions($questions, $model);
        }

        return $result;
    }

    protected function handleModelQuestions($questions, $model)
    {
        $result = [
            'model' => $model,
            'question_count' => 0,
            'total_score' => 0,
            'questions' => [],
        ];

        foreach ($questions as $question) {
            if (isset($this->questionMappings[$question['id']])) {
                $question['stat'] = $this->questionMappings[$question['id']];
            } else {
                $question['stat'] = [
                    'total_count' => 1,
                    'finish_count' => 0,
                    'correct_count' => 0,
                ];
            }
            if ($question['model'] == $model) {
                $result['total_score'] += $question['score'];
                $result['question_count'] += 1;
                $result['questions'][] = $question;
            }
        }

        return $result;
    }

    protected function getQuestionMappings($paperId)
    {
        $cache = $this->getCache();

        $keyName = $this->getQuestionStatCacheKey($paperId);

        $result = $cache->get($keyName);

        if ($result) return $result;

        $rows = $this->findExamQuestionUsers($paperId);

        if ($rows->count() == 0) return [];

        /**
         * 客观题型
         */
        $allowModels = [
            ExamQuestionModel::MODEL_SINGLE_CHOICE,
            ExamQuestionModel::MODEL_MULTIPLE_CHOICE,
            ExamQuestionModel::MODEL_TRUE_FALSE,
            ExamQuestionModel::MODEL_BLANK_FILL,
        ];

        $mappings = [];

        foreach ($rows as $row) {
            $key = $row->question_id;
            if (in_array($row->question_model, $allowModels)) {
                if (!isset($mappings[$key])) {
                    $mappings[$key] = [
                        'total_count' => 1,
                        'finish_count' => 0,
                        'correct_count' => 0,
                    ];
                } else {
                    $mappings[$key]['total_count'] += 1;
                }
                if (strlen($row->user_answer) > 0) {
                    $mappings[$key]['finish_count'] += 1;
                }
                if ($row->user_score > 0) {
                    $mappings[$key]['correct_count'] += 1;
                }
            }
        }

        $cache->save($keyName, $mappings, 1800);

        return $mappings;
    }

    protected function getQuestionStatCacheKey($paperId)
    {
        return "exam_paper_question_stat:{$paperId}";
    }

    protected function findExamQuestionUsers($paperId)
    {
        $paperRepo = new ExamPaperRepo();

        return $paperRepo->findExamQuestionUsers($paperId);
    }

}
