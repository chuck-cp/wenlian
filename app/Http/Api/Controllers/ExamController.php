<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Exam\FavoriteQuestionList as FavoriteQuestionListService;
use App\Services\Logic\Exam\MistakeQuestionList as MistakeQuestionListService;
use App\Services\Logic\Exam\MockAnswerSubmit as MockAnswerSubmitService;
use App\Services\Logic\Exam\MockPaperSubmit as MockPaperSubmitService;
use App\Services\Logic\Exam\MockQuestionList as ExamQuestionListService;
use App\Services\Logic\Exam\Paper\BasicInfo as PaperBasicInfoService;
use App\Services\Logic\Exam\PaperUser as ExamPaperUserService;
use App\Services\Logic\Exam\UnitAnswerSubmit as UnitAnswerSubmitService;
use App\Services\Logic\Exam\UnitFreshQuestionList as UnitFreshQuestionListService;
use App\Services\Logic\Exam\UnitHistoryQuestionList as UnitHistoryQuestionListService;
use App\Services\Logic\Exam\UnitReset as UnitResetService;

/**
 * @RoutePrefix("/api/exam")
 */
class ExamController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/mock/info", name="api.exam.mock_info")
     */
    public function mockInfoAction($id)
    {
        $service = new ExamPaperUserService();

        $paperUser = $service->handle($id);

        $service = new PaperBasicInfoService();

        $paper = $service->handle($paperUser['paper_id']);

        $service = new ExamQuestionListService();

        $questions = $service->handle($id);

        $content = [
            'paper_user' => $paperUser,
            'paper' => $paper,
            'questions' => $questions,
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/info", name="api.exam.unit_info")
     */
    public function unitInfoAction($id)
    {
        $service = new ExamPaperUserService();

        $paperUser = $service->handle($id);

        $service = new PaperBasicInfoService();

        $paper = $service->handle($paperUser['paper_id']);

        $content = [
            'paper_user' => $paperUser,
            'paper' => $paper,
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/questions/fresh", name="api.exam.unit_fresh_questions")
     */
    public function unitFreshQuestionsAction($id)
    {
        $service = new UnitFreshQuestionListService();

        $questions = $service->handle($id);

        return $this->jsonSuccess(['questions' => $questions]);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/questions/history", name="api.exam.unit_history_questions")
     */
    public function unitHistoryQuestionsAction($id)
    {
        $service = new UnitHistoryQuestionListService();

        $pager = $service->handle($id);

        return $this->jsonSuccess(['pager' => $pager]);
    }

    /**
     * @Get("/mistake/questions", name="pi.exam.mistake_questions")
     */
    public function mistakeQuestionsAction()
    {
        $service = new MistakeQuestionListService();

        $pager = $service->handle();

        return $this->jsonSuccess(['pager' => $pager]);
    }

    /**
     * @Get("/favorite/questions", name="api.exam.favorite_questions")
     */
    public function favoriteQuestionsAction()
    {
        $service = new FavoriteQuestionListService();

        $pager = $service->handle();

        return $this->jsonSuccess(['pager' => $pager]);
    }

    /**
     * @Post("/{id:[0-9]+}/mock/paper/submit", name="api.exam.submit_mock_paper")
     */
    public function submitMockPaperAction($id)
    {
        $service = new MockPaperSubmitService();

        $paperUser = $service->handle($id);

        return $this->jsonSuccess(['paper_user' => $paperUser]);
    }

    /**
     * @Post("/{id:[0-9]+}/mock/answer/submit", name="api.exam.submit_mock_answer")
     */
    public function submitMockAnswerAction($id)
    {
        $service = new MockAnswerSubmitService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

    /**
     * @Post("/{id:[0-9]+}/unit/answer/submit", name="api.exam.submit_unit_answer")
     */
    public function submitUnitAnswerAction($id)
    {
        $service = new UnitAnswerSubmitService();

        $userScore = $service->handle($id);

        return $this->jsonSuccess(['user_score' => $userScore]);
    }

    /**
     * @Post("/{id:[0-9]+}/unit/reset", name="api.exam.reset_unit")
     */
    public function resetUnitAction($id)
    {
        $service = new UnitResetService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

}
