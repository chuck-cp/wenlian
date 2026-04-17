<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\Order as OrderModel;
use App\Models\Refund as RefundModel;
use App\Models\Task as TaskModel;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\Order as OrderRepo;
use App\Services\Logic\Service as LogicService;

class TeamRefund extends LogicService
{

    public function handle(GrouponTeamModel $team)
    {
        if ($team->status != GrouponTeamModel::STATUS_ACTIVE) return;

        try {

            $this->db->begin();

            $team->status = GrouponTeamModel::STATUS_CLOSED;

            $team->update();

            $teamRepo = new GrouponTeamRepo();

            $teamUsers = $teamRepo->findFinishedTeamUsers($team->id);

            if ($teamUsers->count() == 0) return;

            $orderIds = kg_array_column($teamUsers->toArray(), 'order_id');

            $orderRepo = new OrderRepo();

            $orders = $orderRepo->findByIds($orderIds);

            if ($orders->count() == 0) return;

            foreach ($orders as $order) {

                if ($order->status != OrderModel::STATUS_DELIVERING) continue;

                $trade = $orderRepo->findFinishedTrade($order->id);

                if (!$trade) continue;

                $refund = new RefundModel();

                $refund->subject = $order->subject;
                $refund->amount = $trade->amount;
                $refund->order_id = $order->id;
                $refund->trade_id = $trade->id;
                $refund->owner_id = $order->owner_id;
                $refund->status = RefundModel::STATUS_APPROVED;
                $refund->apply_note = '团购未成团';
                $refund->review_note = '团购未成团，无条件审批';
                $refund->create();

                $task = new TaskModel();

                $itemInfo = [
                    'refund' => ['id' => $refund->id],
                ];

                $task->item_id = $refund->id;
                $task->item_info = $itemInfo;
                $task->item_type = TaskModel::TYPE_REFUND;
                $task->priority = TaskModel::PRIORITY_MIDDLE;
                $task->status = TaskModel::STATUS_PENDING;
                $task->create();
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('refund');

            $logger->error('Team Refund Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

}
