<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\Order as OrderModel;
use App\Models\Task as TaskModel;
use App\Repos\Groupon as GrouponRepo;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\Order as OrderRepo;
use App\Services\Logic\Service as LogicService;

class TeamDeliver extends LogicService
{

    public function handle(GrouponTeamModel $team)
    {
        if ($team->status != GrouponTeamModel::STATUS_ACTIVE) return;

        $grouponRepo = new GrouponRepo();

        $groupon = $grouponRepo->findById($team->groupon_id);

        try {

            $this->db->begin();

            $team->status = GrouponTeamModel::STATUS_FINISHED;

            $team->update();

            $groupon->finish_team_count += 1;

            $groupon->update();

            $teamRepo = new GrouponTeamRepo();

            $teamUsers = $teamRepo->findFinishedTeamUsers($team->id);

            if ($teamUsers->count() == 0) return;

            $orderIds = kg_array_column($teamUsers->toArray(), 'order_id');

            $orderRepo = new OrderRepo();

            $orders = $orderRepo->findByIds($orderIds);

            if ($orders->count() == 0) return;

            foreach ($orders as $order) {

                if ($order->status != OrderModel::STATUS_DELIVERING) continue;

                $itemInfo = [
                    'order' => ['id' => $order->id],
                ];

                $task = new TaskModel();

                $task->item_id = $order->id;
                $task->item_info = $itemInfo;
                $task->item_type = TaskModel::TYPE_DELIVER;
                $task->create();
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('deliver');

            $logger->error('Team Deliver Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

}
