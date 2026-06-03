<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ExamQuestionList as ExamQuestionListBuilder;
use App\Builders\ReportList as ReportListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Category as CategoryModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\Report as ReportModel;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Repos\Report as ReportRepo;
use App\Services\Category as CategoryService;
use App\Services\Logic\Point\History\CriticizeAccepted as CriticizeAcceptedPointHistory;
use App\Validators\ExamQuestion as ExamQuestionValidator;

class ExamQuestion extends Service
{

    public function getModelTypes()
    {
        return ExamQuestionModel::modelTypes();
    }

    public function getLevelTypes()
    {
        return ExamQuestionModel::levelTypes();
    }

    public function getCategoryOptions()
    {
        $categoryService = new CategoryService();

        return $categoryService->getCategoryOptions(CategoryModel::TYPE_EXAM_QUESTION);
    }

    public function filterQuestions()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['published'] = 1;
        $params['deleted'] = 0;
        $params['parent_id'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $questionRepo = new ExamQuestionRepo();

        $pager = $questionRepo->paginate($params, $sort, $page, $limit);

        return $this->handleQuestions($pager);
    }

    public function getQuestions()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['deleted'] = $params['deleted'] ?? 0;
        $params['parent_id'] = $params['parent_id'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $questionRepo = new ExamQuestionRepo();

        $pager = $questionRepo->paginate($params, $sort, $page, $limit);

        return $this->handleQuestions($pager);
    }

    public function getQuestion($id)
    {
        $question = $this->findOrFail($id);

        $choiceModels = [
            ExamQuestionModel::MODEL_SINGLE_CHOICE,
            ExamQuestionModel::MODEL_MULTIPLE_CHOICE,
        ];

        /**
         * 选项为空，存储时忽略空选项，编辑时回填空选项
         */
        if (in_array($question->model, $choiceModels)) {
            $question->attrs['choices'] = $this->refillAnswerChoices($question->attrs['choices']);
        }

        return $question;
    }

    public function getChildQuestions($id)
    {
        $questionRepo = new ExamQuestionRepo();

        return $questionRepo->findChildQuestions($id);
    }

    public function getReports($id)
    {
        $reportRepo = new ReportRepo();

        $where = [
            'item_id' => $id,
            'item_type' => ReportModel::ITEM_EXAM_QUESTION,
            'reviewed' => 0,
        ];

        $pager = $reportRepo->paginate($where);

        $pager = $this->handleReports($pager);

        return $pager->items;
    }

    public function createQuestion()
    {
        $parentId = $this->request->getPost('parent_id');
        $model = $this->request->getPost('model');

        $questionRepo = new ExamQuestionRepo();

        $validator = new ExamQuestionValidator();

        $question = new ExamQuestionModel();

        $question->model = $validator->checkModel($model);

        if ($parentId > 0) {
            $parent = $validator->checkParent($parentId);
            $maxPriority = $questionRepo->maxChildPriority($parentId);
            $priority = $maxPriority > 0 ? $maxPriority : 10;
            $question->parent_id = $parent->id;
            $question->priority = $priority;
            $question->published = 1;
        }

        $question->create();

        return $question;
    }

    public function updateQuestion($id)
    {
        $question = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new ExamQuestionValidator();

        switch ($question->model) {
            case ExamQuestionModel::MODEL_SINGLE_CHOICE:
                $question = $this->updateSingleChoiceQuestion($question, $post);
                break;
            case ExamQuestionModel::MODEL_MULTIPLE_CHOICE:
                $question = $this->updateMultipleChoiceQuestion($question, $post);
                break;
            case ExamQuestionModel::MODEL_TRUE_FALSE:
                $question = $this->updateTrueFalseQuestion($question, $post);
                break;
            case ExamQuestionModel::MODEL_BLANK_FILL:
                $question = $this->updateBlankFillQuestion($question, $post);
                break;
            case ExamQuestionModel::MODEL_SHORT_ANSWER:
                $question = $this->updateShortAnswerQuestion($question, $post);
                break;
            case ExamQuestionModel::MODEL_COMPLEX_QUESTION:
                $question = $this->updateComplexQuestion($question, $post);
                break;
        }

        if ($question->parent_id > 0) {
            $priority = $validator->checkPriority($post['priority']);
            $this->updatePriority($question, $priority);
            $this->syncParentScore($question->parent_id);
        }

        return $question;
    }

    public function deleteQuestion($id)
    {
        $question = $this->findOrFail($id);

        $question->deleted = 1;

        $question->update();

        return $question;
    }

    public function restoreQuestion($id)
    {
        $question = $this->findOrFail($id);

        $question->deleted = 0;

        $question->update();

        return $question;
    }

    public function batchPublish()
    {
        $ids = $this->request->getPost('ids', ['trim', 'int']);

        $questionRepo = new ExamQuestionRepo();

        $questions = $questionRepo->findByIds($ids);

        if ($questions->count() == 0) return;

        foreach ($questions as $question) {
            $question->published = 1;
            $question->update();
        }
    }

    public function batchDelete()
    {
        $ids = $this->request->getPost('ids', ['trim', 'int']);

        $questionRepo = new ExamQuestionRepo();

        $questions = $questionRepo->findByIds($ids);

        if ($questions->count() == 0) return;

        foreach ($questions as $question) {
            $question->deleted = 1;
            $question->update();
        }
    }

    public function report($id)
    {
        $accepted = $this->request->getPost('accepted', 'int', 0);

        $question = $this->findOrFail($id);

        $reportRepo = new ReportRepo();

        $reports = $reportRepo->findItemPendingReports($question->id, ReportModel::ITEM_EXAM_QUESTION);

        if ($reports->count() > 0) {

            foreach ($reports as $report) {

                $report->accepted = $accepted;
                $report->reviewed = 1;
                $report->update();

                if ($accepted == 1) {
                    $service = new CriticizeAcceptedPointHistory();
                    $service->handle($report);
                }
            }
        }

        $question->report_count = 0;

        $question->update();
    }

    protected function findOrFail($id)
    {
        $validator = new ExamQuestionValidator();

        return $validator->checkExamQuestion($id);
    }

    protected function refillAnswerChoices(array $choices)
    {
        $result = [];

        foreach (['A', 'B', 'C', 'D'] as $key) {
            $result[$key] = $choices[$key] ?? '';
        }

        return $result;
    }

    protected function handleCommonData(array $post)
    {
        $validator = new ExamQuestionValidator();

        $data = [];

        if (isset($post['category_id'])) {
            $data['category_id'] = $validator->checkCategoryId($post['category_id']);
        }

        if (isset($post['topic'])) {
            $data['topic'] = $validator->checkTopic($post['topic']);
        }

        if (isset($post['solution'])) {
            $data['solution'] = $validator->checkSolution($post['solution']);
        }

        if (isset($post['score'])) {
            $data['score'] = $validator->checkScore($post['score']);
        }

        if (isset($post['level'])) {
            $data['level'] = $validator->checkLevel($post['level']);
        }

        if (isset($post['featured'])) {
            $data['featured'] = $validator->checkFeatureStatus($post['featured']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        return $data;
    }

    protected function updatePriority(ExamQuestionModel $question, $priority)
    {
        $validator = new ExamQuestionValidator();

        $question->priority = $validator->checkPriority($priority);

        $question->update();
    }

    protected function syncParentScore($parentId)
    {
        $questionRepo = new ExamQuestionRepo();

        $question = $questionRepo->findById($parentId);

        if ($question->model != ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
            return;
        }

        $score = $questionRepo->sumParentScore($question->id);

        $question->score = $score;

        $question->update();
    }

    protected function updateSingleChoiceQuestion(ExamQuestionModel $question, array $post)
    {
        $commonData = $this->handleCommonData($post);

        $validator = new ExamQuestionValidator();

        $myData = [];

        $attrs = $question->attrs;

        if (isset($post['choices'])) {
            $attrs['choices'] = $validator->checkAnswerChoices($post['choices']);
        }

        if (isset($post['answer'])) {
            $myData['answer'] = $validator->checkSingleChoiceAnswer($post['answer']);
        }

        $myData['attrs'] = $attrs;

        $data = $commonData + $myData;

        $question->assign($data);

        $question->update();

        return $question;
    }

    protected function updateMultipleChoiceQuestion(ExamQuestionModel $question, array $post)
    {
        $commonData = $this->handleCommonData($post);

        $validator = new ExamQuestionValidator();

        $myData = [];

        if (isset($post['answer'])) {
            $myData['answer'] = $validator->checkMultipleChoiceAnswer($post['answer']);
        }

        $attrs = $question->attrs;

        if (isset($post['choices'])) {
            $attrs['choices'] = $validator->checkAnswerChoices($post['choices']);
        }

        $myData['attrs'] = $attrs;

        $data = $commonData + $myData;

        $question->assign($data);

        $question->update();

        return $question;
    }

    protected function updateTrueFalseQuestion(ExamQuestionModel $question, array $post)
    {
        $commonData = $this->handleCommonData($post);

        $validator = new ExamQuestionValidator();

        $myData = [];

        if (isset($post['answer'])) {
            $myData['answer'] = $validator->checkTrueFalseAnswer($post['answer']);
        }

        $data = $commonData + $myData;

        $question->assign($data);

        $question->update();

        return $question;
    }

    protected function updateBlankFillQuestion(ExamQuestionModel $question, array $post)
    {
        $commonData = $this->handleCommonData($post);

        $validator = new ExamQuestionValidator();

        $myData = [];

        if (isset($post['answer'])) {
            $myData['answer'] = $validator->checkBlankFillAnswer($post['answer']);
        }

        $data = $commonData + $myData;

        $question->assign($data);

        $question->update();

        return $question;
    }

    protected function updateShortAnswerQuestion(ExamQuestionModel $question, array $post)
    {
        $commonData = $this->handleCommonData($post);

        $validator = new ExamQuestionValidator();

        $myData = [];

        if (isset($post['answer'])) {
            $myData['answer'] = $validator->checkShortAnswer($post['answer']);
        }

        $data = $commonData + $myData;

        $question->assign($data);

        $question->update();

        return $question;
    }

    protected function updateComplexQuestion(ExamQuestionModel $question, array $post)
    {
        $commonData = $this->handleCommonData($post);

        $myData = [];

        $data = $commonData + $myData;

        $question->assign($data);

        $question->update();

        $this->syncParentScore($question->id);

        return $question;
    }

    protected function handleQuestions($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ExamQuestionListBuilder();

            $items = $pager->items->toArray();

            $pipeA = $builder->handleCategories($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

    protected function handleReports($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ReportListBuilder();

            $items = $pager->items->toArray();

            $pipeA = $builder->handleUsers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
