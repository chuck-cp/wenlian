<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Danmu as DanmuService;

/**
 * @RoutePrefix("/admin/danmu")
 */
class DanmuController extends Controller
{

    /**
     * @Get("/search", name="admin.danmu.search")
     */
    public function searchAction()
    {

    }

    /**
     * @Get("/list", name="admin.danmu.list")
     */
    public function listAction()
    {
        $danmuService = new DanmuService();

        $pager = $danmuService->getDanmus();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.danmu.update")
     */
    public function updateAction($id)
    {
        $danmuService = new DanmuService();

        $danmuService->updateDanmu($id);

        $content = ['msg' => '更新弹幕成功'];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.danmu.delete")
     */
    public function deleteAction($id)
    {
        $danmuService = new DanmuService();

        $danmuService->deleteDanmu($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '删除弹幕成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.danmu.restore")
     */
    public function restoreAction($id)
    {
        $danmuService = new DanmuService();

        $danmuService->restoreDanmu($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '还原弹幕成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Route("/{id:[0-9]+}/moderate", name="admin.danmu.moderate")
     */
    public function moderateAction($id)
    {
        $danmuService = new DanmuService();

        if ($this->request->isPost()) {

            $danmuService->moderate($id);

            $location = $this->url->get(['for' => 'admin.mod.danmus']);

            $content = [
                'location' => $location,
                'msg' => '审核弹幕成功',
            ];

            return $this->jsonSuccess($content);
        }

        $reasons = $danmuService->getReasons();
        $danmu = $danmuService->getDanmuInfo($id);

        $this->view->setVar('reasons', $reasons);
        $this->view->setVar('danmu', $danmu);
    }

    /**
     * @Post("/moderate/batch", name="admin.danmu.batch_moderate")
     */
    public function batchModerateAction()
    {
        $danmuService = new DanmuService();

        $danmuService->batchModerate();

        $location = $this->url->get(['for' => 'admin.mod.danmus']);

        $content = [
            'location' => $location,
            'msg' => '批量审核成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/delete/batch", name="admin.danmu.batch_delete")
     */
    public function batchDeleteAction()
    {
        $danmuService = new DanmuService();

        $danmuService->batchDelete();

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '批量删除成功',
        ];

        return $this->jsonSuccess($content);
    }

}
