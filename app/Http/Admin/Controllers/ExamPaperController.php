<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\ExamPaper as ExamPaperService;
use App\Http\Admin\Services\User as UserService;
use App\Http\Admin\Services\ExamPaperLearning as ExamPaperLearningService;
use App\Http\Admin\Services\ExamPaperLearningExport as ExamPaperLearningExportService;
use App\Http\Admin\Services\ExamPaperUser as ExamPaperUserService;
use App\Http\Admin\Services\ExamPaperSummaryStat as ExamPaperSummaryStatService;
use App\Http\Admin\Services\ExamPaperRangeStat as ExamPaperRangeStatService;
use App\Http\Admin\Services\ExamPaperQuestionStat as ExamPaperQuestionStatService;
use App\Http\Admin\Services\ExamPaperUserExport as ExamPaperUserExportService;
use App\Http\Admin\Services\ExamPaperUserImport as ExamPaperUserImportService;
use App\Models\Category as CategoryModel;
use App\Models\ExamPaper as ExamPaperModel;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/admin/exam/paper")
 */
class ExamPaperController extends Controller
{

    /**
     * @Get("/category", name="admin.exam_paper.category")
     */
    public function categoryAction()
    {
        $location = $this->url->get(
            ['for' => 'admin.category.list'],
            ['type' => CategoryModel::TYPE_EXAM_PAPER]
        );

        return $this->response->redirect($location);
    }

    /**
     * @Get("/search", name="admin.exam_paper.search")
     */
    public function searchAction()
    {
        $paperService = new ExamPaperService();

        $categoryOptions = $paperService->getCategoryOptions();
        $teacherOptions = $paperService->getTeacherOptions();
        $levelTypes = $paperService->getPaperLevelTypes();
        $examTypes = $paperService->getExamTypes();
        $packTypes = $paperService->getPackTypes();
        $gradeTypes = $paperService->getGradeTypes();

        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('teacher_options', $teacherOptions);
        $this->view->setVar('level_types', $levelTypes);
        $this->view->setVar('exam_types', $examTypes);
        $this->view->setVar('pack_types', $packTypes);
        $this->view->setVar('grade_types', $gradeTypes);
    }

    /**
     * @Get("/list", name="admin.exam_paper.list")
     */
    public function listAction()
    {
        $paperService = new ExamPaperService();

        $pager = $paperService->getPapers();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/add", name="admin.exam_paper.add")
     */
    public function addAction()
    {
        $paperService = new ExamPaperService();

        $packTypes = $paperService->getPackTypes();

        $this->view->setVar('pack_types', $packTypes);
    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.exam_paper.edit")
     */
    public function editAction($id)
    {
        $paperService = new ExamPaperService();

        $paper = $paperService->getPaper($id);

        $categoryIds = [];

        if ($paper->pack_type == ExamPaperModel::PACK_TYPE_RANDOM) {
            $categoryIds = $paper->attrs['category_ids'] ?? [];
        }

        $xmTags = $paperService->getXmTags($id);
        $gradeTypes = $paperService->getGradeTypes();
        $teacherOptions = $paperService->getTeacherOptions();
        $categoryOptions = $paperService->getCategoryOptions();
        $durationOptions = $paperService->getDurationOptions();
        $studyExpiryOptions = $paperService->getStudyExpiryOptions();
        $refundExpiryOptions = $paperService->getRefundExpiryOptions();
        $paperLevelTypes = $paperService->getPaperLevelTypes();
        $questionLevelTypes = $paperService->getQuestionLevelTypes();
        $questionXmCategories = $paperService->getQuestionXmCategories($categoryIds);

        $this->view->setVar('paper', $paper);
        $this->view->setVar('xm_tags', $xmTags);
        $this->view->setVar('grade_types', $gradeTypes);
        $this->view->setVar('teacher_options', $teacherOptions);
        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('duration_options', $durationOptions);
        $this->view->setVar('study_expiry_options', $studyExpiryOptions);
        $this->view->setVar('refund_expiry_options', $refundExpiryOptions);
        $this->view->setVar('paper_level_types', $paperLevelTypes);
        $this->view->setVar('question_level_types', $questionLevelTypes);
        $this->view->setVar('question_xm_categories', $questionXmCategories);
    }

    /**
     * @Get("/{id:[0-9]+}/questions", name="admin.exam_paper.questions")
     */
    public function questionsAction($id)
    {
        $paperService = new ExamPaperService();

        $paper = $paperService->getPaper($id);
        $questions = $paperService->getQuestions($id);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('paper', $paper);
        $this->view->setVar('questions', $questions);
    }

    /**
     * @Post("/create", name="admin.exam_paper.create")
     */
    public function createAction()
    {
        $paperService = new ExamPaperService();

        $paper = $paperService->createPaper();

        $location = $this->url->get([
            'for' => 'admin.exam_paper.edit',
            'id' => $paper->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '创建试卷成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.exam_paper.update")
     */
    public function updateAction($id)
    {
        $paperService = new ExamPaperService();

        $paperService->updatePaper($id);

        $content = ['msg' => '更新试卷成功'];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.exam_paper.delete")
     */
    public function deleteAction($id)
    {
        $paperService = new ExamPaperService();

        $paperService->deletePaper($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '删除试卷成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.exam_paper.restore")
     */
    public function restoreAction($id)
    {
        $paperService = new ExamPaperService();

        $paperService->restorePaper($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '还原试卷成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/analysis", name="admin.exam_paper.analysis")
     */
    public function analysisAction($id)
    {
        $service = new ExamPaperService();
        $paper = $service->getPaper($id);

        $service = new ExamPaperSummaryStatService();
        $summaryStat = $service->handle($id);

        $service = new ExamPaperRangeStatService();
        $rangeStat = $service->handle($id);

        $service = new ExamPaperQuestionStatService();
        $questionStat = $service->handle($id);

        $this->view->setVar('paper', $paper);
        $this->view->setVar('summary_stat', $summaryStat);
        $this->view->setVar('range_stat', $rangeStat);
        $this->view->setVar('question_stat', $questionStat);
    }

    /**
     * @Get("/{id:[0-9]+}/learnings", name="admin.exam_paper.learnings")
     */
    public function learningsAction($id)
    {
        $paperService = new ExamPaperService();
        $paper = $paperService->getPaper($id);

        $learningService = new ExamPaperLearningService();
        $pager = $learningService->getLearnings($id);

        $this->view->setVar('paper', $paper);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/learning/search", name="admin.exam_paper.search_learning")
     */
    public function searchLearningAction($id)
    {
        $service = new ExamPaperService();
        $paper = $service->getPaper($id);

        $service = new ExamPaperUserService();
        $sourceTypes = $service->getSourceTypes();

        $this->view->pick('exam_paper/search_learning');
        $this->view->setVar('source_types', $sourceTypes);
        $this->view->setVar('paper', $paper);
    }

    /**
     * @Get("/{id:[0-9]+}/learning/export", name="admin.exam_paper.export_learning")
     */
    public function exportLearningAction($id)
    {
        $exportService = new ExamPaperLearningExportService();

        $result = $exportService->handle($id);

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.exam_paper.search_learning', 'id' => $id],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/{id:[0-9]+}/users", name="admin.exam_paper.users")
     */
    public function usersAction($id)
    {
        $service = new ExamPaperService();
        $paper = $service->getPaper($id);

        $service = new ExamPaperUserService();
        $pager = $service->getUsers($id);

        $this->view->setVar('paper', $paper);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/user/search", name="admin.exam_paper.search_user")
     */
    public function searchUserAction($id)
    {
        $service = new ExamPaperService();
        $paper = $service->getPaper($id);

        $service = new ExamPaperUserService();
        $sourceTypes = $service->getSourceTypes();

        $this->view->pick('exam_paper/search_user');
        $this->view->setVar('source_types', $sourceTypes);
        $this->view->setVar('paper', $paper);
    }

    /**
     * @Get("/{id:[0-9]+}/user/add", name="admin.exam_paper.add_user")
     */
    public function addUserAction($id)
    {
        $service = new ExamPaperService();

        $paper = $service->getPaper($id);

        $this->view->pick('exam_paper/add_user');
        $this->view->setVar('paper', $paper);
    }

    /**
     * @Post("/{id:[0-9]+}/user/create", name="admin.exam_paper.create_user")
     */
    public function createUserAction($id)
    {
        $service = new ExamPaperUserService();

        $service->create($id);

        $location = $this->url->get([
            'for' => 'admin.exam_paper.users',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/user/import", name="admin.exam_paper.import_user")
     */
    public function importUserAction($id)
    {
        $importService = new ExamPaperUserImportService();

        $importService->handle($id);

        $location = $this->url->get([
            'for' => 'admin.exam_paper.users',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '导入学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/user/export", name="admin.exam_paper.export_user")
     */
    public function exportUserAction($id)
    {
        $exportService = new ExamPaperUserExportService();

        $result = $exportService->handle($id);

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.exam_paper.search_user', 'id' => $id],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/user/edit/{id:[0-9]+}", name="admin.exam_paper.edit_user")
     */
    public function editUserAction($id)
    {
        $service = new ExamPaperUserService();
        $paperUser = $service->get($id);

        $service = new ExamPaperService();
        $paper = $service->getPaper($paperUser->paper_id);

        $service = new UserService();
        $user = $service->getUser($paperUser->user_id);

        $this->view->pick('exam_paper/edit_user');
        $this->view->setVar('paper_user', $paperUser);
        $this->view->setVar('paper', $paper);
        $this->view->setVar('user', $user);
    }

    /**
     * @Post("/user/update/{id:[0-9]+}", name="admin.exam_paper.update_user")
     */
    public function updateUserAction($id)
    {
        $service = new ExamPaperUserService();

        $service->update($id);

        $content = ['msg' => '更新学员成功'];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/user/delete/{id:[0-9]+}", name="admin.exam_paper.delete_user")
     */
    public function deleteUserAction($id)
    {
        $service = new ExamPaperUserService();

        $service->delete($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '删除学员成功',
        ];

        return $this->jsonSuccess($content);
    }

}
