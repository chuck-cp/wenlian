<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\KgSale as KgSaleModel;
use App\Models\Order as OrderModel;
use App\Models\Refund as RefundModel;
use App\Models\Task as TaskModel;
use App\Models\Trade as TradeModel;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Repos\CourseUser as CourseUserRepo;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Repos\Order as OrderRepo;
use App\Repos\Refund as RefundRepo;
use App\Repos\Trade as TradeRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\RefundFinish as RefundFinishNotice;
use App\Services\Pay\Alipay as AlipayService;
use App\Services\Pay\Wxpay as WxpayService;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;

class Refund extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start refund task ------' . PHP_EOL;

        $tradeRepo = new TradeRepo();
        $orderRepo = new OrderRepo();
        $refundRepo = new RefundRepo();

        $refund = $refundRepo->findById($task->item_id);
        $trade = $tradeRepo->findById($refund->trade_id);
        $order = $orderRepo->findById($refund->order_id);

        if ($refund->status != RefundModel::STATUS_APPROVED) {
            $task->status = TaskModel::STATUS_CANCELED;
            $task->update();
            return;
        }

        try {

            $this->db->begin();

            $this->handleTradeRefund($trade, $refund);
            $this->handleOrderRefund($order);

            $refund->status = RefundModel::STATUS_FINISHED;
            $refund->update();

            $trade->status = TradeModel::STATUS_REFUNDED;
            $trade->update();

            $order->status = OrderModel::STATUS_REFUNDED;
            $order->update();

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            $this->handleInvoiceBalance($order);
            $this->handleRefundFinishNotice($refund);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $refund->status = RefundModel::STATUS_FAILED;
            $refund->update();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $logger = $this->getLogger('refund');

            $logger->error('Refund Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task->toArray(),
                ]));
        }

        echo '------ end refund task ------' . PHP_EOL;
    }

    /**
     * 处理交易退款
     *
     * @param TradeModel $trade
     * @param RefundModel $refund
     */
    protected function handleTradeRefund(TradeModel $trade, RefundModel $refund)
    {
        $response = false;

        if ($trade->channel == TradeModel::CHANNEL_ALIPAY) {

            $alipay = new AlipayService();

            $response = $alipay->refund($refund);

        } elseif ($trade->channel == TradeModel::CHANNEL_WXPAY) {

            $wxpay = new WxpayService();

            $response = $wxpay->refund($refund);
        }

        if (!$response) {
            throw new \RuntimeException('Trade Refund Failed');
        }
    }

    /**
     * 处理订单退款
     *
     * @param OrderModel $order
     */
    protected function handleOrderRefund(OrderModel $order)
    {
        switch ($order->item_type) {
            case KgSaleModel::ITEM_COURSE:
                $this->handleCourseOrderRefund($order);
                break;
            case KgSaleModel::ITEM_PACKAGE:
                $this->handlePackageOrderRefund($order);
                break;
            case KgSaleModel::ITEM_VIP:
                $this->handleVipOrderRefund($order);
                break;
            case KgSaleModel::ITEM_EXAM_PAPER:
                $this->handleExamPaperOrderRefund($order);
                break;
            case KgSaleModel::ITEM_ARTICLE:
                $this->handleArticleOrderRefund($order);
                break;
            case KgSaleModel::ITEM_PAY_TEST:
                $this->handleTestOrderRefund($order);
                break;
        }
    }

    /**
     * 处理课程订单退款
     *
     * @param OrderModel $order
     */
    protected function handleCourseOrderRefund(OrderModel $order)
    {
        $courseUserRepo = new CourseUserRepo();

        $courseUser = $courseUserRepo->findCourseUser($order->item_id, $order->owner_id);

        if ($courseUser) {
            $courseUser->deleted = 1;
            $courseUser->update();
        }
    }

    /**
     * 处理套餐订单退款
     *
     * @param OrderModel $order
     */
    protected function handlePackageOrderRefund(OrderModel $order)
    {
        $courseUserRepo = new CourseUserRepo();

        foreach ($order->item_info['courses'] as $course) {

            $courseUser = $courseUserRepo->findCourseUser($course['id'], $order->owner_id);

            if ($courseUser) {
                $courseUser->deleted = 1;
                $courseUser->update();
            }
        }
    }

    /**
     * 处理会员订单退款
     *
     * @param OrderModel $order
     */
    protected function handleVipOrderRefund(OrderModel $order)
    {
        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $itemInfo = $order->item_info;

        $diffTime = "-{$itemInfo['vip']['expiry']} months";
        $baseTime = $itemInfo['vip']['expiry_time'];

        $user->vip_expiry_time = strtotime($diffTime, $baseTime);

        if ($user->vip_expiry_time < time()) {
            $user->vip = 0;
        }

        $user->update();
    }

    /**
     * 处理试卷订单退款
     *
     * @param OrderModel $order
     */
    protected function handleExamPaperOrderRefund(OrderModel $order)
    {
        $paperUserRepo = new ExamPaperUserRepo();

        $paperUser = $paperUserRepo->findDebutPaperUser($order->item_id, $order->owner_id);

        if ($paperUser) {
            $paperUser->deleted = 1;
            $paperUser->update();
        }
    }

    /**
     * 处理专栏订单退款
     *
     * @param OrderModel $order
     */
    protected function handleArticleOrderRefund(OrderModel $order)
    {
        $articleUserRepo = new ArticleUserRepo();

        $articleUser = $articleUserRepo->findArticleUser($order->item_id, $order->owner_id);

        if ($articleUser) {
            $articleUser->deleted = 1;
            $articleUser->update();
        }
    }

    /**
     * 处理测试订单退款
     *
     * @param OrderModel $order
     */
    protected function handleTestOrderRefund(OrderModel $order)
    {

    }

    /**
     * 处理开票额度
     *
     * @param OrderModel $order
     */
    protected function handleInvoiceBalance(OrderModel $order)
    {
        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($order->owner_id);

        $balance->invoice -= $order->amount;

        $balance->update();
    }

    /**
     * @param RefundModel $refund
     */
    protected function handleRefundFinishNotice(RefundModel $refund)
    {
        $notice = new RefundFinishNotice();

        $notice->createTask($refund);
    }

}
