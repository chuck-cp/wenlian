<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\ExamQuestion as ExamQuestionService;
use App\Http\Admin\Services\ExamQuestionImport as ExamQuestionImportService;
use App\Models\Category as CategoryModel;
use App\Models\ExamQuestion as ExamQuestionModel;

/**
 * @RoutePrefix("/admin/exam/question")
 */
class ExamQuestionController extends Controller
{

    /**
     * @Get("/category", name="admin.exam_question.category")
     */
    public function categoryAction()
    {
        $location = $this->url->get(
            ['for' => 'admin.category.list'],
            ['type' => CategoryModel::TYPE_EXAM_QUESTION]
        );

        return $this->response->redirect($location);
    }

    /**
     * @Get("/search", name="admin.exam_question.search")
     */
    public function searchAction()
    {
        $questionService = new ExamQuestionService();

        $categoryOptions = $questionService->getCategoryOptions();
        $modelTypes = $questionService->getModelTypes();
        $levelTypes = $questionService->getLevelTypes();

        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('model_types', $modelTypes);
        $this->view->setVar('level_types', $levelTypes);
    }

    /**
     * @Get("/filter", name="admin.exam_question.filter")
     */
    public function filterAction()
    {
        $questionService = new ExamQuestionService();

        $pager = $questionService->filterQuestions();

        $categoryOptions = $questionService->getCategoryOptions();
        $modelTypes = $questionService->getModelTypes();
        $levelTypes = $questionService->getLevelTypes();

        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('model_types', $modelTypes);
        $this->view->setVar('level_types', $levelTypes);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/list", name="admin.exam_question.list")
     */
    public function listAction()
    {
        $questionService = new ExamQuestionService();

        $pager = $questionService->getQuestions();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/add", name="admin.exam_question.add")
     */
    public function addAction()
    {
        $questionService = new ExamQuestionService();

        $modelTypes = $questionService->getModelTypes();
        $levelTypes = $questionService->getLevelTypes();

        $this->view->setVar('model_types', $modelTypes);
        $this->view->setVar('level_types', $levelTypes);
    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.exam_question.edit")
     */
    public function editAction($id)
    {
        $questionService = new ExamQuestionService();

        $question = $questionService->getQuestion($id);
        $childQuestions = $questionService->getChildQuestions($id);
        $categoryOptions = $questionService->getCategoryOptions();
        $modelTypes = $questionService->getModelTypes();
        $levelTypes = $questionService->getLevelTypes();

        if ($question->model == ExamQuestionModel::MODEL_SINGLE_CHOICE) {
            $this->view->pick('exam_question/edit_single_choice');
        } elseif ($question->model == ExamQuestionModel::MODEL_MULTIPLE_CHOICE) {
            $this->view->pick('exam_question/edit_multiple_choice');
        } elseif ($question->model == ExamQuestionModel::MODEL_TRUE_FALSE) {
            $this->view->pick('exam_question/edit_true_false');
        } elseif ($question->model == ExamQuestionModel::MODEL_BLANK_FILL) {
            $this->view->pick('exam_question/edit_blank_fill');
        } elseif ($question->model == ExamQuestionModel::MODEL_SHORT_ANSWER) {
            $this->view->pick('exam_question/edit_short_answer');
        } elseif ($question->model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
            $this->view->pick('exam_question/edit_complex_question');
        }

        $this->view->setVar('question', $question);
        $this->view->setVar('child_questions', $childQuestions);
        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('model_types', $modelTypes);
        $this->view->setVar('level_types', $levelTypes);
    }

    /**
     * @Post("/import", name="admin.exam_question.import")
     */
    public function importAction()
    {
        $service = new ExamQuestionImportService();

        $service->handle();

        $location = $this->url->get(['for' => 'admin.exam_question.list']);

        $content = [
            'location' => $location,
            'msg' => '导入试题成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/create", name="admin.exam_question.create")
     */
    public function createAction()
    {
        $questionService = new ExamQuestionService();

        $question = $questionService->createQuestion();

        $location = $this->url->get([
            'for' => 'admin.exam_question.edit',
            'id' => $question->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '创建试题成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.exam_question.update")
     */
    public function updateAction($id)
    {
        $questionService = new ExamQuestionService();

        $question = $questionService->updateQuestion($id);

        $content = ['msg' => '更新试题成功'];

        if ($question->parent_id > 0) {
            $content['location'] = $this->url->get([
                'for' => 'admin.exam_question.edit',
                'id' => $question->parent_id,
            ]);
        }

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.exam_question.delete")
     */
    public function deleteAction($id)
    {
        $questionService = new ExamQuestionService();

        $questionService->deleteQuestion($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '删除试题成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.exam_question.restore")
     */
    public function restoreAction($id)
    {
        $questionService = new ExamQuestionService();

        $questionService->restoreQuestion($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '还原试题成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Route("/{id:[0-9]+}/report", name="admin.exam_question.report")
     */
    public function reportAction($id)
    {
        $questionService = new ExamQuestionService();

        if ($this->request->isPost()) {

            $questionService->report($id);

            $location = $this->url->get(['for' => 'admin.report.exam_questions']);

            $content = [
                'location' => $location,
                'msg' => '处理挑错成功',
            ];

            return $this->jsonSuccess($content);
        }

        $question = $questionService->getQuestion($id);
        $reports = $questionService->getReports($id);

        $this->view->setVar('question', $question);
        $this->view->setVar('reports', $reports);
    }

    /**
     * @Post("/publish/batch", name="admin.exam_question.batch_publish")
     */
    public function batchPublishAction()
    {
        $questionService = new ExamQuestionService();

        $questionService->batchPublish();

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '批量发布成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/delete/batch", name="admin.exam_question.batch_delete")
     */
    public function batchDeleteAction()
    {
        $questionService = new ExamQuestionService();

        $questionService->batchDelete();

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '批量删除成功',
        ];

        return $this->jsonSuccess($content);
    }

}
