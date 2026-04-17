<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\GrouponTeamUser as GrouponTeamUserModel;
use App\Models\Order as OrderModel;
use App\Models\Task as TaskModel;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\Order as OrderRepo;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class GrouponDeliver extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start deliver task ------' . PHP_EOL;

        $teamRepo = new GrouponTeamRepo();

        try {

            $this->db->begin();

            $team = $teamRepo->findById($task->item_id);

            $this->handleTeamDeliver($team);

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $logger = $this->getLogger('deliver');

            $logger->error('Groupon Deliver Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task,
                ]));
        }

        echo '------ end deliver task ------' . PHP_EOL;
    }

    protected function handleTeamDeliver(GrouponTeamModel $team)
    {
        $teamUsers = $this->findFinishedTeamUsers($team->id);

        if ($teamUsers->count() == 0) return;

        $orderIds = kg_array_column($teamUsers->toArray(), 'order_id');

        $orderRepo = new OrderRepo();

        $orders = $orderRepo->findByIds($orderIds);

        if ($orders->count() == 0) return;

        foreach ($orders as $order) {

            if ($order->status != OrderModel::STATUS_DELIVERING) {
                continue;
            }

            $task = new TaskModel();

            $task->item_id = $order->id;
            $task->item_type = TaskModel::TYPE_DELIVER;
            $task->create();
        }
    }

    /**
     * @param int $teamId
     * @return ResultsetInterface|Resultset|GrouponTeamUserModel[]
     */
    public function findFinishedTeamUsers($teamId)
    {
        $status = GrouponTeamUserModel::STATUS_FINISHED;

        return GrouponTeamUserModel::query()
            ->where('team_id = :team_id:', ['team_id' => $teamId])
            ->andWhere('status = :status:', ['status' => $status])
            ->execute();
    }

}
