<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
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

        $taskLockId = LockUtil::addLock($taskLockKey, 300);

        if (!$taskLockId) return;

        $orders = $this->findOrders();

        echo sprintf('pending orders: %s', $orders->count()) . PHP_EOL;

        if ($orders->count() == 0) return;

        echo '------ start close order task ------' . PHP_EOL;

        foreach ($orders as $order) {
            $this->closeOrder($order);
        }

        echo '------ end close order task ------' . PHP_EOL;

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function closeOrder(OrderModel $order)
    {
        try {

            $this->db->begin();

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

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('order');

            $logger->error('Close Order Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'order' => $order,
                ]));
        }
    }

    /**
     * 查找待关闭订单
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|OrderModel[]
     */
    protected function findOrders($limit = 500)
    {
        $status = OrderModel::STATUS_PENDING;

        $time = time() - 12 * 3600;

        /**
         * 秒杀订单有独立的关闭逻辑，不需要处理
         */
        $excludePromotionTypes = [
            OrderModel::PROMOTION_FLASH_SALE,
        ];

        return OrderModel::query()
            ->where('status = :status:', ['status' => $status])
            ->andWhere('create_time < :time:', ['time' => $time])
            ->notInWhere('promotion_type', $excludePromotionTypes)
            ->limit($limit)
            ->execute();
    }

}
