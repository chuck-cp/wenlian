<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\Order as OrderModel;
use App\Services\Logic\Coupon\CouponOrderTrait;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CloseOrderTask extends Task
{

    use CouponOrderTrait;

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $orders = $this->findOrders();

        echo sprintf('pending orders: %s', $orders->count()) . PHP_EOL;

        if ($orders->count() == 0) return;

        echo '------ start close order task ------' . PHP_EOL;

        foreach ($orders as $order) {

            $order->status = OrderModel::STATUS_CLOSED;

            $promotionType = $order->promotion_type;
            $couponId = $order->promotion_id;
            $userId = $order->owner_id;

            if ($promotionType == OrderModel::PROMOTION_COUPON) {
                $order->promotion_id = 0;
                $order->promotion_type = 0;
                $order->promotion_info = [];
            }

            $order->update();

            if ($promotionType == OrderModel::PROMOTION_COUPON) {
                $this->revokeAppliedCoupon($couponId, $userId);
            }
        }

        echo '------ end close order task ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    /**
     * 查找待关闭订单
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|OrderModel[]
     */
    protected function findOrders($limit = 1000)
    {
        $status = OrderModel::STATUS_PENDING;
        $time = time() - 12 * 3600;

        return OrderModel::query()
            ->where('status = :status:', ['status' => $status])
            ->andWhere('create_time < :time:', ['time' => $time])
            ->limit($limit)
            ->execute();
    }

}
