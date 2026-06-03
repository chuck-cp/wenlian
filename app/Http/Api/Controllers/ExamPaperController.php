<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Models\ExamPaper as ExamPaperModel;
use App\Services\Logic\Exam\Paper\CategoryList as ExamPaperCategoryListService;
use App\Services\Logic\Exam\Paper\LatestMockUserList as LatestMockUserListService;
use App\Services\Logic\Exam\Paper\LatestUnitUserList as LatestUnitUserListService;
use App\Services\Logic\Exam\Paper\MockPaperJoin as MockPaperJoinService;
use App\Services\Logic\Exam\Paper\TopMockUserList as TopMockUserListService;
use App\Services\Logic\Exam\Paper\PaperFavorite as ExamPaperFavoriteService;
use App\Services\Logic\Exam\Paper\PaperMockHistory as ExamPaperMockHistoryService;
use App\Services\Logic\Exam\Paper\PaperInfo as ExamPaperInfoService;
use App\Services\Logic\Exam\Paper\PaperList as ExamPaperListService;
use App\Services\Logic\Exam\Paper\UnitPaperJoin as UnitPaperJoinService;

/**
 * @RoutePrefix("/api/exam/paper")
 */
class ExamPaperController extends Controller
{

    /**
     * @Get("/categories", name="api.exam_paper.categories")
     */
    public function categoriesAction()
    {
        $service = new ExamPaperCategoryListService();

        $categories = $service->handle();

        return $this->jsonSuccess(['categories' => $categories]);
    }

    /**
     * @Get("/list", name="api.exam_paper.list")
     */
    public function listAction()
    {
        $service = new ExamPaperListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/{id:[0-9]+}/info", name="api.exam_paper.info")
     */
    public function infoAction($id)
    {
        $service = new ExamPaperInfoService();

        $paper = $service->handle($id);

        if ($paper['deleted'] == 1) {
            $this->notFound();
        }

        if ($paper['published'] == 0) {
            $this->notFound();
        }

        return $this->jsonSuccess(['paper' => $paper]);
    }

    /**
     * @Get("/{id:[0-9]+}/mock/users/latest", name="api.exam_paper.latest_mock_users")
     */
    public function latestMockUsersAction($id)
    {
        $service = new LatestMockUserListService();

        $items = $service->handle($id);

        return $this->jsonPaginate(['items' => $items]);
    }

    /**
     * @Get("/{id:[0-9]+}/mock/users/top", name="api.exam_paper.top_mock_users")
     */
    public function topMockUsersAction($id)
    {
        $service = new TopMockUserListService();

        $items = $service->handle($id);

        return $this->jsonPaginate(['items' => $items]);
    }

    /**
     * @Get("/{id:[0-9]+}/mock/history", name="api.exam_paper.mock_history")
     */
    public function mockHistoryAction($id)
    {
        $service = new ExamPaperMockHistoryService();

        $pager = $service->handle($id);

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/users/latest", name="api.exam_paper.latest_unit_users")
     */
    public function latestUnitUsersAction($id)
    {
        $service = new LatestUnitUserListService();

        $items = $service->handle($id);

        return $this->jsonPaginate(['items' => $items]);
    }

    /**
     * @Post("/{id:[0-9]+}/join", name="api.exam_paper.join")
     */
    public function joinAction($id)
    {
        $service = new ExamPaperInfoService();

        $paper = $service->handle($id);

        $paperUser = null;

        if ($paper['exam_type'] == ExamPaperModel::EXAM_TYPE_MOCK) {
            $service = new MockPaperJoinService();
            $paperUser = $service->handle($id);
        } elseif ($paper['exam_type'] == ExamPaperModel::EXAM_TYPE_UNIT) {
            $service = new UnitPaperJoinService();
            $paperUser = $service->handle($id);
        }

        return $this->jsonSuccess(['paper_user' => $paperUser]);
    }

    /**
     * @Post("/{id:[0-9]+}/favorite", name="api.exam_paper.favorite")
     */
    public function favoriteAction($id)
    {
        $service = new ExamPaperFavoriteService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '收藏成功' : '取消收藏成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

}
