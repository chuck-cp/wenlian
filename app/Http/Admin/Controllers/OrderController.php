<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Order as OrderService;
use App\Http\Admin\Services\OrderExport as OrderExportService;

/**
 * @RoutePrefix("/admin/order")
 */
class OrderController extends Controller
{

    /**
     * @Get("/search", name="admin.order.search")
     */
    public function searchAction()
    {
        $orderService = new OrderService();

        $itemTypes = $orderService->getItemTypes();
        $statusTypes = $orderService->getStatusTypes();
        $promotionTypes = $orderService->getPromotionTypes();

        $this->view->setVar('item_types', $itemTypes);
        $this->view->setVar('status_types', $statusTypes);
        $this->view->setVar('promotion_types', $promotionTypes);
    }

    /**
     * @Get("/export", name="admin.order.export")
     */
    public function exportAction()
    {
        $exportService = new OrderExportService();

        $result = $exportService->handle();

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.order.search'],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/list", name="admin.order.list")
     */
    public function listAction()
    {
        $orderService = new OrderService();

        $pager = $orderService->getOrders();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/show", name="admin.order.show")
     */
    public function showAction($id)
    {
        $orderService = new OrderService();

        $order = $orderService->getOrder($id);
        $trades = $orderService->getTrades($order->id);
        $refunds = $orderService->getRefunds($order->id);
        $account = $orderService->getAccount($order->owner_id);
        $user = $orderService->getUser($order->owner_id);

        $this->view->setVar('order', $order);
        $this->view->setVar('trades', $trades);
        $this->view->setVar('refunds', $refunds);
        $this->view->setVar('account', $account);
        $this->view->setVar('user', $user);
    }

    /**
     * @Get("/{id:[0-9]+}/status/history", name="admin.order.status_history")
     */
    public function statusHistoryAction($id)
    {
        $orderService = new OrderService();

        $statusHistory = $orderService->getStatusHistory($id);

        $this->view->pick('order/status_history');
        $this->view->setVar('status_history', $statusHistory);
    }

}
