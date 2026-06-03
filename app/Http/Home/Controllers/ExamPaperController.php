<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Http\Home\Services\ExamPaperQuery as ExamPaperQueryService;
use App\Models\ExamPaper as ExamPaperModel;
use App\Services\Logic\Exam\Paper\LatestMockUserList as LatestMockUserListService;
use App\Services\Logic\Exam\Paper\LatestUnitUserList as LatestUnitUserListService;
use App\Services\Logic\Exam\Paper\PaperFavorite as ExamPaperFavoriteService;
use App\Services\Logic\Exam\Paper\PaperMockHistory as ExamPaperHistoryService;
use App\Services\Logic\Exam\Paper\PaperInfo as ExamPaperInfoService;
use App\Services\Logic\Exam\Paper\MockPaperJoin as MockPaperJoinService;
use App\Services\Logic\Exam\Paper\UnitPaperJoin as UnitPaperJoinService;
use App\Services\Logic\Exam\Paper\PaperList as ExamPaperListService;
use App\Services\Logic\Exam\Paper\TopMockUserList as TopMockUserListService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/exam/paper")
 */
class ExamPaperController extends Controller
{

    /**
     * @Get("/list", name="home.exam_paper.list")
     */
    public function listAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getExamPaperListUrl();
            return $this->response->redirect($location);
        }

        $service = new ExamPaperQueryService();

        $topCategories = $service->handleTopCategories();
        $subCategories = $service->handleSubCategories();

        $examTypes = $service->handleExamTypes();
        $packTypes = $service->handlePackTypes();
        $levels = $service->handleLevels();
        $sorts = $service->handleSorts();
        $params = $service->getParams();

        $this->seo->prependTitle('测评');

        $this->view->setVar('top_categories', $topCategories);
        $this->view->setVar('sub_categories', $subCategories);
        $this->view->setVar('exam_types', $examTypes);
        $this->view->setVar('pack_types', $packTypes);
        $this->view->setVar('levels', $levels);
        $this->view->setVar('sorts', $sorts);
        $this->view->setVar('params', $params);
    }

    /**
     * @Get("/pager", name="home.exam_paper.pager")
     */
    public function pagerAction()
    {
        $service = new ExamPaperListService();

        $pager = $service->handle();

        $pager->target = 'paper-list';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}", name="home.exam_paper.show")
     */
    public function showAction($id)
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getExamPaperInfoUrl($id);
            return $this->response->redirect($location);
        }

        $service = new ExamPaperInfoService();

        $paper = $service->handle($id);

        if ($paper['deleted'] == 1) {
            $this->notFound();
        }

        if ($paper['published'] == 0) {
            $this->notFound();
        }

        $this->seo->prependTitle(['考试', $paper['title']]);
        $this->seo->setKeywords($paper['keywords']);
        $this->seo->setDescription($paper['summary']);

        if ($paper['exam_type'] == ExamPaperModel::EXAM_TYPE_MOCK) {
            $this->view->pick('exam_paper/show_mock');
        } elseif ($paper['exam_type'] == ExamPaperModel::EXAM_TYPE_UNIT) {
            $this->view->pick('exam_paper/show_unit');
        }

        $this->view->setVar('paper', $paper);
    }

    /**
     * @Get("/{id:[0-9]+}/mock/users/latest", name="home.exam_paper.latest_mock_users")
     */
    public function latestMockUsersAction($id)
    {
        $service = new LatestMockUserListService();

        $items = $service->handle($id);

        $this->view->pick('exam_paper/latest_mock_users');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('items', $items);
    }

    /**
     * @Get("/{id:[0-9]+}/mock/users/top", name="home.exam_paper.top_mock_users")
     */
    public function topMockUsersAction($id)
    {
        $service = new topMockUserListService();

        $items = $service->handle($id);

        $this->view->pick('exam_paper/top_mock_users');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('items', $items);
    }

    /**
     * @Get("/{id:[0-9]+}/mock/history", name="home.exam_paper.mock_history")
     */
    public function mockHistoryAction($id)
    {
        $service = new ExamPaperHistoryService();

        $pager = $service->handle($id);
        $pager->target = 'history-list';

        $this->view->pick('exam_paper/mock_history');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/unit/users/latest", name="home.exam_paper.latest_unit_users")
     */
    public function latestUnitUsersAction($id)
    {
        $service = new LatestUnitUserListService();

        $items = $service->handle($id);

        $this->view->pick('exam_paper/latest_unit_users');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('items', $items);
    }

    /**
     * @Post("/{id:[0-9]+}/join", name="home.exam_paper.join")
     */
    public function joinAction($id)
    {
        $service = new ExamPaperInfoService();

        $paper = $service->handle($id);

        $location = $this->url->get([
            'for' => 'home.exam_paper.show',
            'id' => $paper['id'],
        ]);

        if ($paper['exam_type'] == ExamPaperModel::EXAM_TYPE_MOCK) {
            $service = new MockPaperJoinService();
            $paperUser = $service->handle($id);
            $location = $this->url->get([
                'for' => 'home.exam.mock_explore',
                'id' => $paperUser->id,
            ]);
        } elseif ($paper['exam_type'] == ExamPaperModel::EXAM_TYPE_UNIT) {
            $service = new UnitPaperJoinService();
            $paperUser = $service->handle($id);
            $location = $this->url->get([
                'for' => 'home.exam.unit_explore',
                'id' => $paperUser->id,
            ]);
        }

        return $this->jsonSuccess(['location' => $location]);
    }

    /**
     * @Post("/{id:[0-9]+}/favorite", name="home.exam_paper.favorite")
     */
    public function favoriteAction($id)
    {
        $service = new ExamPaperFavoriteService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '收藏成功' : '取消收藏成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

}
