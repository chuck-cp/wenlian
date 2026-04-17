<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Http\Home\Services\ExamQuestion as ExamQuestionService;
use App\Services\Logic\Exam\FavoriteQuestionList as FavoriteQuestionListService;
use App\Services\Logic\Exam\MistakeQuestionList as MistakeQuestionListService;
use App\Services\Logic\Exam\MockAnswerMark as MockAnswerMarkService;
use App\Services\Logic\Exam\MockAnswerSubmit as MockAnswerSubmitService;
use App\Services\Logic\Exam\MockPaperGrade as MockPaperGradeService;
use App\Services\Logic\Exam\MockPaperSubmit as MockPaperSubmitService;
use App\Services\Logic\Exam\MockQuestionList as MockQuestionListService;
use App\Services\Logic\Exam\Paper\BasicInfo as PaperBasicInfoService;
use App\Services\Logic\Exam\PaperUser as ExamPaperUserService;
use App\Services\Logic\Exam\UnitAnswerSubmit as UnitAnswerSubmitService;
use App\Services\Logic\Exam\UnitFreshQuestionList as UnitFreshQuestionListService;
use App\Services\Logic\Exam\UnitHistoryQuestionList as UnitHistoryQuestionListService;
use App\Services\Logic\Exam\UnitReset as UnitResetService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/exam")
 */
class ExamController extends Controller
{

    /**
     * @Get("/mistake/explore", name="home.exam.mistake_explore")
     */
    public function mistakeExploreAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getMistakeExamExploreUrl();
            return $this->response->redirect($location);
        }

        $service = new ExamQuestionService();

        $modelTypes = $service->getModelTypes();

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('model_types', $modelTypes);
        $this->view->pick('exam/mistake_explore');
    }

    /**
     * @Get("/favorite/explore", name="home.exam.favorite_explore")
     */
    public function favoriteExploreAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getFavoriteExamExploreUrl();
            return $this->response->redirect($location);
        }

        $service = new ExamQuestionService();

        $modelTypes = $service->getModelTypes();

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('model_types', $modelTypes);
        $this->view->pick('exam/favorite_explore');
    }

    /**
     * @Get("/{id:[0-9]+}/mock/explore", name="home.exam.mock_explore")
     */
    public function mockExploreAction($id)
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getMockExamExploreUrl($id);
            return $this->response->redirect($location);
        }

        $service = new ExamPaperUserService();

        $paperUser = $service->handle($id);

        $service = new PaperBasicInfoService();

        $paper = $service->handle($paperUser['paper_id']);

        $service = new MockQuestionListService();

        $questions = $service->handle($id);

        $this->seo->prependTitle($paper['title']);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('exam/mock_explore');
        $this->view->setVar('paper', $paper);
        $this->view->setVar('paper_user', $paperUser);
        $this->view->setVar('questions', $questions);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/explore", name="home.exam.unit_explore")
     */
    public function unitExploreAction($id)
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getUnitExamExploreUrl($id);
            return $this->response->redirect($location);
        }

        $service = new ExamPaperUserService();

        $paperUser = $service->handle($id);

        $service = new ExamQuestionService();

        $modelTypes = $service->getModelTypes();

        $service = new PaperBasicInfoService();

        $paper = $service->handle($paperUser['paper_id']);

        $this->seo->prependTitle($paper['title']);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('exam/unit_explore');
        $this->view->setVar('paper_user', $paperUser);
        $this->view->setVar('model_types', $modelTypes);
        $this->view->setVar('paper', $paper);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/questions/fresh", name="home.exam.unit_fresh_questions")
     */
    public function unitFreshQuestionsAction($id)
    {
        $service = new UnitFreshQuestionListService();

        $questions = $service->handle($id);

        return $this->jsonSuccess(['questions' => $questions]);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/questions/history", name="home.exam.unit_history_questions")
     */
    public function unitHistoryQuestionsAction($id)
    {
        $service = new UnitHistoryQuestionListService();

        $pager = $service->handle($id);

        return $this->jsonSuccess(['pager' => $pager]);
    }

    /**
     * @Get("/mistake/questions", name="home.exam.mistake_questions")
     */
    public function mistakeQuestionsAction()
    {
        $service = new MistakeQuestionListService();

        $pager = $service->handle();

        return $this->jsonSuccess(['pager' => $pager]);
    }

    /**
     * @Get("/favorite/questions", name="home.exam.favorite_questions")
     */
    public function favoriteQuestionsAction()
    {
        $service = new FavoriteQuestionListService();

        $pager = $service->handle();

        return $this->jsonSuccess(['pager' => $pager]);
    }

    /**
     * @Post("/{id:[0-9]+}/mock/paper/submit", name="home.exam.submit_mock_paper")
     */
    public function submitMockPaperAction($id)
    {
        $service = new MockPaperSubmitService();

        $paperUser = $service->handle($id);

        return $this->jsonSuccess(['paper_user' => $paperUser]);
    }

    /**
     * @Post("/{id:[0-9]+}/mock/paper/grade", name="home.exam.grade_mock_paper")
     */
    public function gradeMockPaperAction($id)
    {
        $service = new MockPaperGradeService();

        $paperUser = $service->handle($id);

        return $this->jsonSuccess(['paper_user' => $paperUser]);
    }

    /**
     * @Post("/{id:[0-9]+}/mock/answer/submit", name="home.exam.submit_mock_answer")
     */
    public function submitMockAnswerAction($id)
    {
        $service = new MockAnswerSubmitService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

    /**
     * @Post("/{id:[0-9]+}/unit/answer/submit", name="home.exam.submit_unit_answer")
     */
    public function submitUnitAnswerAction($id)
    {
        $service = new UnitAnswerSubmitService();

        $userScore = $service->handle($id);

        return $this->jsonSuccess(['user_score' => $userScore]);
    }

    /**
     * @Post("/{id:[0-9]+}/mock/answer/mark", name="home.exam.mark_mock_answer")
     */
    public function markMockAnswerAction($id)
    {
        $service = new MockAnswerMarkService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

    /**
     * @Post("/{id:[0-9]+}/unit/reset", name="home.exam.reset_unit")
     */
    public function resetUnitAction($id)
    {
        $service = new UnitResetService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

}
