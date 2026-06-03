<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Groupon as GrouponService;
use App\Http\Admin\Services\GrouponTeam as GrouponTeamService;

/**
 * @RoutePrefix("/admin/groupon")
 */
class GrouponController extends Controller
{

    /**
     * @Get("/list", name="admin.groupon.list")
     */
    public function listAction()
    {
        $service = new GrouponService();

        $pager = $service->getGroupons();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/search", name="admin.groupon.search")
     */
    public function searchAction()
    {
        $service = new GrouponService();

        $itemTypes = $service->getItemTypes();
        $xmCourses = $service->getXmCourses();
        $xmPackages = $service->getXmPackages();
        $xmExamPapers = $service->getXmExamPapers();
        $xmArticles = $service->getXmArticles();
        $xmVips = $service->getXmVips();

        $this->view->setVar('item_types', $itemTypes);
        $this->view->setVar('xm_courses', $xmCourses);
        $this->view->setVar('xm_packages', $xmPackages);
        $this->view->setVar('xm_exam_papers', $xmExamPapers);
        $this->view->setVar('xm_articles', $xmArticles);
        $this->view->setVar('xm_vips', $xmVips);
    }

    /**
     * @Get("/add", name="admin.groupon.add")
     */
    public function addAction()
    {
        $service = new GrouponService();

        $itemTypes = $service->getItemTypes();
        $xmCourses = $service->getXmCourses();
        $xmPackages = $service->getXmPackages();
        $xmExamPapers = $service->getXmExamPapers();
        $xmArticles = $service->getXmArticles();
        $xmVips = $service->getXmVips();

        $this->view->setVar('item_types', $itemTypes);
        $this->view->setVar('xm_courses', $xmCourses);
        $this->view->setVar('xm_packages', $xmPackages);
        $this->view->setVar('xm_exam_papers', $xmExamPapers);
        $this->view->setVar('xm_articles', $xmArticles);
        $this->view->setVar('xm_vips', $xmVips);
    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.groupon.edit")
     */
    public function editAction($id)
    {
        $service = new GrouponService();

        $groupon = $service->getGroupon($id);

        $this->view->setVar('groupon', $groupon);
    }

    /**
     * @Post("/create", name="admin.groupon.create")
     */
    public function createAction()
    {
        $service = new GrouponService();

        $groupon = $service->createGroupon();

        $location = $this->url->get([
            'for' => 'admin.groupon.edit',
            'id' => $groupon->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加商品成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.groupon.update")
     */
    public function updateAction($id)
    {
        $service = new GrouponService();

        $service->updateGroupon($id);

        $location = $this->url->get(['for' => 'admin.groupon.list']);

        $content = [
            'location' => $location,
            'msg' => '更新商品成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.groupon.delete")
     */
    public function deleteAction($id)
    {
        $grouponService = new GrouponService();

        $grouponService->deleteGroupon($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除商品成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.groupon.restore")
     */
    public function restoreAction($id)
    {
        $grouponService = new GrouponService();

        $grouponService->restoreGroupon($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '还原商品成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/teams", name="admin.groupon.teams")
     */
    public function teamsAction($id)
    {
        $service = new GrouponService();

        $groupon = $service->getGroupon($id);

        $service = new GrouponTeamService();

        $pager = $service->getTeams($id);

        $this->view->setVar('groupon', $groupon);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/team/{id:[0-9]+}/users", name="admin.groupon.team_users")
     */
    public function teamUsersAction($id)
    {
        $service = new GrouponTeamService();

        $pager = $service->getTeamUsers($id);

        $this->view->pick('groupon/team_users');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Post("/team/{id:[0-9]+}/close", name="admin.groupon.close_team")
     */
    public function closeTeamAction($id)
    {
        $service = new GrouponTeamService();

        $service->closeTeam($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '关闭队伍成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/team/{id:[0-9]+}/refund", name="admin.groupon.refund_team")
     */
    public function refundTeamAction($id)
    {
        $service = new GrouponTeamService();

        $service->refundTeam($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '解散队伍成功',
        ];

        return $this->jsonSuccess($content);
    }

}
