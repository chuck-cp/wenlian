<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Listeners;

use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\GrouponTeamUser as GrouponTeamUserModel;
use App\Models\Order as OrderModel;
use App\Models\Task as TaskModel;
use App\Models\Trade as TradeModel;
use App\Repos\Groupon as GrouponRepo;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\GrouponTeamUser as GrouponTeamUserRepo;
use App\Repos\Order as OrderRepo;
use App\Services\Logic\FlashSale\UserOrderCache as FlashSaleLockCache;
use Phalcon\Events\Event as PhEvent;
use Phalcon\Logger\Adapter\File as FileLogger;

class Trade extends Listener
{

    /**
     * @var FileLogger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = $this->getLogger('trade');
    }

    public function afterPay(PhEvent $event, $source, TradeModel $trade)
    {
        try {

            $this->db->begin();

            $trade->status = TradeModel::STATUS_FINISHED;

            $trade->update();

            $orderRepo = new OrderRepo();

            $order = $orderRepo->findById($trade->order_id);

            /**
             * 单开变量，afterUpdate会改变变量类型
             */
            $promotionInfo = $order->promotion_info;

            $order->status = OrderModel::STATUS_DELIVERING;
            $order->update();

            /**
             * 团购订单不能立即发货
             */
            if ($order->promotion_type == OrderModel::PROMOTION_GROUPON) {

                $grouponRepo = new GrouponRepo();
                $groupon = $grouponRepo->findById($promotionInfo['groupon']['id']);

                $teamRepo = new GrouponTeamRepo();
                $team = $teamRepo->findById($promotionInfo['team']['id']);

                $teamUserRepo = new GrouponTeamUserRepo();
                $teamUser = $teamUserRepo->findByOrderId($order->id);

                $teamUser->status = GrouponTeamUserModel::STATUS_FINISHED;
                $teamUser->update();

                $team->finish_order_count += 1;

                if ($teamUser->user_id == $team->leader_id) {
                    $groupon->total_team_count += 1;
                }

                if ($team->status = GrouponTeamModel::STATUS_PENDING) {
                    $team->status = GrouponTeamModel::STATUS_ACTIVE;
                }

                if ($team->target_order_count == $team->finish_order_count) {

                    $team->status = GrouponTeamModel::STATUS_FINISHED;

                    $task = new TaskModel();

                    $task->item_id = $team->id;
                    $task->item_type = TaskModel::TYPE_GROUPON_DELIVER;
                    $task->create();
                }

                $team->update();

                if ($team->status == GrouponTeamModel::STATUS_FINISHED) {
                    $groupon->finish_team_count += 1;
                }

                $groupon->update();

            } else {

                $task = new TaskModel();

                $task->item_id = $order->id;
                $task->item_type = TaskModel::TYPE_DELIVER;
                $task->create();
            }

            $this->db->commit();

            /**
             * 解除秒杀锁定
             */
            if ($order->promotion_type == OrderModel::PROMOTION_FLASH_SALE) {
                $cache = new FlashSaleLockCache();
                $cache->delete($order->owner_id, $order->promotion_id);
            }

        } catch (\Exception $e) {

            $this->db->rollback();

            $this->logger->error('After Pay Event Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'trade' => $trade,
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

}
