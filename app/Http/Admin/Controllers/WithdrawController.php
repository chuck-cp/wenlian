<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Withdraw as WithdrawService;
use App\Http\Admin\Services\WithdrawExport as WithdrawExportService;

/**
 * @RoutePrefix("/admin/withdraw")
 */
class WithdrawController extends Controller
{

    /**
     * @Get("/search", name="admin.withdraw.search")
     */
    public function searchAction()
    {
        $withdrawService = new WithdrawService();

        $statusTypes = $withdrawService->getStatusTypes();

        $this->view->setVar('status_types', $statusTypes);
    }

    /**
     * @Get("/export", name="admin.withdraw.export")
     */
    public function exportAction()
    {
        $exportService = new WithdrawExportService();

        $result = $exportService->handle();

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.withdraw.search'],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/list", name="admin.withdraw.list")
     */
    public function listAction()
    {
        $withdrawService = new WithdrawService();

        $pager = $withdrawService->getWithdraws();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/show", name="admin.withdraw.show")
     */
    public function showAction($id)
    {
        $withdrawService = new WithdrawService();

        $withdraw = $withdrawService->getWithdraw($id);
        $withdrawAccount = $withdrawService->getWithdrawAccount($withdraw->account_id);

        $this->view->setVar('withdraw_account', $withdrawAccount);
        $this->view->setVar('withdraw', $withdraw);
    }

    /**
     * @Get("/{id:[0-9]+}/status/history", name="admin.withdraw.status_history")
     */
    public function statusHistoryAction($id)
    {
        $withdrawService = new WithdrawService();

        $statusHistory = $withdrawService->getStatusHistory($id);

        $this->view->pick('withdraw/status_history');
        $this->view->setVar('status_history', $statusHistory);
    }

    /**
     * @Post("/{id:[0-9]+}/review", name="admin.withdraw.review")
     */
    public function reviewAction($id)
    {
        $withdrawService = new WithdrawService();

        $withdrawService->reviewWithdraw($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '审核提现成功',
        ];

        return $this->jsonSuccess($content);
    }

}
