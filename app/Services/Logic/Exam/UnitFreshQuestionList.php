<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamPaperQuestion as ExamPaperQuestionRepo;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class UnitFreshQuestionList extends LogicService
{

    /**
     * @var ExamPaperUserModel
     */
    protected $paperUser;

    /**
     * @var ExamPaperModel
     */
    protected $paper;

    /**
     * 返回数量
     *
     * @var int
     */
    protected $limit;

    /**
     * 题目类型
     *
     * @var int
     */
    protected $model;

    use ExamPaperTrait;

    public function handle($id)
    {
        $limit = $this->request->getQuery('limit', 'int', 20);
        $model = $this->request->getQuery('model', 'int', 0);

        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        $paper = $this->checkExamPaperCache($paperUser->paper_id);

        $this->paperUser = $paperUser;

        $this->paper = $paper;

        $this->limit = $limit;

        $this->model = $model;

        $questionIds = $this->getRemainQuestionIds();

        $result = [];

        foreach ($questionIds as $id) {
            $result[] = ['id' => $id];
        }

        return $result;
    }

    protected function getRemainQuestionIds()
    {
        $questionIds = [];

        if ($this->paper->pack_type == ExamPaperModel::PACK_TYPE_MANUAL) {
            $questionIds = $this->getRemainManualQuestionIds();
        } elseif ($this->paper->pack_type == ExamPaperModel::PACK_TYPE_RANDOM) {
            $questionIds = $this->getRemainRandomQuestionIds();
        }

        return array_slice($questionIds, 0, $this->limit);
    }

    protected function getRemainManualQuestionIds()
    {
        $allQuestionIds = $this->getManualQuestionIds();

        $usedQuestionIds = $this->getUsedQuestionIds();

        return array_diff($allQuestionIds, $usedQuestionIds);
    }

    /**
     * 按组卷模板返回各题型可用题目
     *
     * @return array
     */
    protected function getRemainRandomQuestionIds()
    {
        $categoryIds = $this->paper->attrs['category_ids'];

        $conditions = $this->paper->attrs['conditions'];

        $questionRepo = new ExamQuestionRepo();

        $allQuestions = $questionRepo->findByCategoryIds($categoryIds);

        $usedQuestionIds = $this->getUsedQuestionIds();

        $result = [];

        foreach ($conditions as $condition) {
            $modelOk = true;
            if ($this->model > 0 && $condition['model'] != $this->model) {
                $modelOk = false;
            }
            if ($modelOk && $condition['limit'] > 0) {
                $modelQuestionIds = $this->getModelQuestionIds($allQuestions, $condition);
                $diffQuestionIds = array_diff($modelQuestionIds, $usedQuestionIds);
                if (count($diffQuestionIds) > 0) {
                    $randQuestionIds = kg_array_rand($diffQuestionIds, $condition['limit']);
                    $randQuestionIds = is_array($randQuestionIds) ? $randQuestionIds : [$randQuestionIds];
                    $result = array_merge($result, $randQuestionIds);
                }
            }
        }

        return $result;
    }

    /**
     * @param $allQuestions ExamQuestionModel[]
     * @param $conditions array
     * @return array
     */
    protected function getModelQuestionIds($allQuestions, $conditions)
    {
        $questionIds = [];

        foreach ($allQuestions as $question) {
            $case1 = $question->parent_id == 0;
            $case2 = $question->model == $conditions['model'];
            $case3 = empty($conditions['level']) || in_array($question->level, $conditions['level']);
            if ($case1 && $case2 && $case3) {
                $questionIds[] = $question->id;
            }
        }

        return $questionIds;
    }

    /**
     * 获取人工组卷的题目
     *
     * @return array
     */
    protected function getManualQuestionIds()
    {
        $repo = new ExamPaperQuestionRepo();

        $rows = $repo->findByPaperId($this->paper->id);

        $result = [];

        if ($rows->count() == 0) return $result;

        foreach ($rows as $row) {
            $modelOk = true;
            if ($this->model > 0 && $row->question_model != $this->model) {
                $modelOk = false;
            }
            if ($modelOk) {
                $result[] = $row->question_id;
            }
        }

        return $result;
    }

    /**
     * 获取使用过的题目
     *
     * @return array
     */
    protected function getUsedQuestionIds()
    {
        $repo = new ExamQuestionUserRepo();

        $rows = $repo->findByPaperUserId($this->paperUser->id);

        $result = [];

        if ($rows->count() == 0) return $result;

        foreach ($rows as $row) {
            if ($row->finished == 1) {
                $result[] = $row->question_parent_id > 0 ? $row->question_parent_id : $row->question_id;
            }
        }

        return array_unique($result);
    }

}
