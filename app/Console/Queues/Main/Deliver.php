<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\KgProduct as KgProductModel;
use App\Models\Order as OrderModel;
use App\Models\Refund as RefundModel;
use App\Models\Task as TaskModel;
use App\Models\Trade as TradeModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Order as OrderRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\User as UserRepo;
use App\Repos\Vip as VipRepo;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Services\Logic\Deliver\ArticleDeliver as ArticleDeliverService;
use App\Services\Logic\Deliver\CourseDeliver as CourseDeliverService;
use App\Services\Logic\Deliver\ExamPaperDeliver as ExamPaperDeliverService;
use App\Services\Logic\Deliver\PackageDeliver as PackageDeliverService;
use App\Services\Logic\Deliver\VipDeliver as VipDeliverService;
use App\Services\Logic\Deliver\WithdrawAccountDeliver as WithdrawAccountDeliverService;
use App\Services\Logic\Notice\External\OrderFinish as OrderFinishNotice;
use App\Services\Logic\Point\History\OrderConsume as OrderConsumePointHistory;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Deliver extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start deliver task ------' . PHP_EOL;

        $orderRepo = new OrderRepo();

        $order = $orderRepo->findById($task->item_id);

        try {

            $this->db->begin();

            switch ($order->item_type) {
                case KgProductModel::ITEM_COURSE:
                    $this->handleCourseOrder($order);
                    break;
                case KgProductModel::ITEM_PACKAGE:
                    $this->handlePackageOrder($order);
                    break;
                case KgProductModel::ITEM_VIP:
                    $this->handleVipOrder($order);
                    break;
                case KgProductModel::ITEM_EXAM_PAPER:
                    $this->handleExamPaperOrder($order);
                    break;
                case KgProductModel::ITEM_ARTICLE:
                    $this->handleArticleOrder($order);
                    break;
                case KgProductModel::ITEM_PAY_ACCOUNT_VERIFY:
                    $this->handlePayAccountVerifyOrder($order);
                    break;
            }

            $order->status = OrderModel::STATUS_FINISHED;
            $order->update();

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            if ($this->isNormalOrder($order)) {
                $this->handleInvoiceBalance($order);
                $this->handleOrderConsumePoint($order);
                $this->handleOrderFinishNotice($order);
            }

            if ($this->isAffiliateOrder($order)) {
                $this->createAffiliateSettleTask($order);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $this->handleOrderRefund($order);

            $logger = $this->getLogger('deliver');

            $logger->error('Deliver Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task->toArray(),
                ]));
        }

        echo '------ end deliver task ------' . PHP_EOL;
    }

    /**
     * 判断订单能否分销抽成
     *
     * @param OrderModel $order
     * @return bool
     */
    protected function isAffiliateOrder(OrderModel $order)
    {
        $scopes = [
            KgProductModel::ITEM_COURSE,
            KgProductModel::ITEM_PACKAGE,
            KgProductModel::ITEM_VIP,
            KgProductModel::ITEM_EXAM_PAPER,
            KgProductModel::ITEM_ARTICLE,
        ];

        return in_array($order->item_type, $scopes);
    }

    /**
     * 判断是否常规订单，排除了(测试|验证)类型
     *
     * @param OrderModel $order
     * @return bool
     */
    protected function isNormalOrder(OrderModel $order)
    {
        $scopes = [
            KgProductModel::ITEM_COURSE,
            KgProductModel::ITEM_PACKAGE,
            KgProductModel::ITEM_VIP,
            KgProductModel::ITEM_EXAM_PAPER,
            KgProductModel::ITEM_ARTICLE,
        ];

        return in_array($order->item_type, $scopes);
    }

    protected function handleCourseOrder(OrderModel $order)
    {
        $courseRepo = new CourseRepo();

        $course = $courseRepo->findById($order->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $service = new CourseDeliverService();

        $service->handle($course, $user);
    }

    protected function handlePackageOrder(OrderModel $order)
    {
        $packageRepo = new PackageRepo();

        $package = $packageRepo->findById($order->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $service = new PackageDeliverService();

        $service->handle($package, $user);
    }

    protected function handleVipOrder(OrderModel $order)
    {
        $vipRepo = new VipRepo();

        $vip = $vipRepo->findById($order->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $service = new VipDeliverService();

        $service->handle($vip, $user);

        /**
         * 先下单购买商品，发现会员有优惠，于是购买会员，再回头购买商品
         * 自动关闭未支付订单，让用户可以使用会员价再次下单
         */
        $this->closePendingOrders($user->id);
    }

    protected function handleExamPaperOrder(OrderModel $order)
    {
        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($order->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $service = new ExamPaperDeliverService();

        $service->handle($paper, $user);
    }

    protected function handleArticleOrder(OrderModel $order)
    {
        $articleRepo = new ArticleRepo();

        $article = $articleRepo->findById($order->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $service = new ArticleDeliverService();

        $service->handle($article, $user);
    }

    protected function handlePayAccountVerifyOrder(OrderModel $order)
    {
        $accountRepo = new WithdrawAccountRepo();

        $account = $accountRepo->findById($order->item_id);

        $service = new WithdrawAccountDeliverService();

        $service->handle($account);
    }

    protected function closePendingOrders($userId)
    {
        $orders = $this->findUserPendingOrders($userId);

        if ($orders->count() == 0) return;

        $itemTypes = [
            KgProductModel::ITEM_COURSE,
            KgProductModel::ITEM_PACKAGE,
            KgProductModel::ITEM_EXAM_PAPER,
            KgProductModel::ITEM_ARTICLE,
        ];

        foreach ($orders as $order) {
            $case1 = in_array($order->item_type, $itemTypes);
            $case2 = $order->promotion_type == 0;
            if ($case1 && $case2) {
                $order->status = OrderModel::STATUS_CLOSED;
                $order->update();
            }
        }
    }

    protected function handleOrderConsumePoint(OrderModel $order)
    {
        $service = new OrderConsumePointHistory();

        $service->handle($order);
    }

    protected function handleOrderFinishNotice(OrderModel $order)
    {
        $notice = new OrderFinishNotice();

        $notice->createTask($order);
    }

    protected function handleInvoiceBalance(OrderModel $order)
    {
        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($order->owner_id);

        $balance->invoice += $order->amount;

        $balance->update();
    }

    protected function handleOrderRefund(OrderModel $order)
    {
        $trade = $this->findFinishedTrade($order->id);

        if (!$trade) return;

        $refund = new RefundModel();

        $refund->owner_id = $order->owner_id;
        $refund->order_id = $order->id;
        $refund->trade_id = $trade->id;
        $refund->subject = $order->subject;
        $refund->amount = $order->amount;
        $refund->apply_note = '开通服务失败，自动退款';
        $refund->review_note = '自动操作';

        $refund->create();

        $task = new TaskModel();

        $task->item_id = $refund->id;
        $task->item_type = TaskModel::TYPE_REFUND;
        $task->priority = TaskModel::PRIORITY_HIGH;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    protected function createAffiliateSettleTask(OrderModel $order)
    {
        $settings = $this->getSettings('affiliate');

        if ($settings['v1_com_enabled'] == 0) return;

        $task = new TaskModel();

        $task->item_id = $order->id;
        $task->item_type = TaskModel::TYPE_AFFILIATE_SETTLE;

        $task->create();
    }

    /**
     * @param int $orderId
     * @return Model|TradeModel
     */
    protected function findFinishedTrade($orderId)
    {
        $status = TradeModel::STATUS_FINISHED;

        return TradeModel::findFirst([
            'conditions' => ['order_id = :order_id: AND status = :status:'],
            'bind' => ['order_id' => $orderId, 'status' => $status],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $userId
     * @return ResultsetInterface|Resultset|OrderModel[]
     */
    protected function findUserPendingOrders($userId)
    {
        $status = OrderModel::STATUS_PENDING;

        return OrderModel::query()
            ->where('owner_id = :owner_id:', ['owner_id' => $userId])
            ->andWhere('status = :status:', ['status' => $status])
            ->execute();
    }

}
