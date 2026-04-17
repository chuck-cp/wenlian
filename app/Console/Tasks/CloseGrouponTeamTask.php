<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Library\Utils\Lock as LockUtil;
use App\Models\GrouponTeam as GrouponTeamModel;
use App\Repos\Groupon as GrouponRepo;
use App\Services\Logic\Groupon\TeamClose as GrouponTeamCloseService;
use App\Services\Logic\Groupon\TeamDeliver as GrouponTeamDeliverService;
use App\Services\Logic\Groupon\TeamRefund as GrouponTeamRefundService;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CloseGrouponTeamTask extends Task
{

    public function mainAction()
    {
        $taskLockKey = $this->getTaskLockKey();

        $taskLockId = LockUtil::addLock($taskLockKey);

        if (!$taskLockId) return;

        $this->handleActiveTeams();
        $this->handlePendingTeams();

        LockUtil::releaseLock($taskLockKey, $taskLockId);
    }

    protected function handleActiveTeams()
    {
        $teams = $this->findActiveTeams();

        echo sprintf('active teams: %s', $teams->count()) . PHP_EOL;

        if ($teams->count() == 0) return;

        echo '------ start handle active team task ------' . PHP_EOL;

        $grouponRepo = new GrouponRepo();

        foreach ($teams as $team) {

            $finished = $team->target_order_count == $team->finish_order_count;

            $groupon = $grouponRepo->findById($team->groupon_id);

            $virtual = $groupon->virtual_partner == 1;

            /**
             * 完成拼团或虚拟拼团则发货，否则退款
             */
            if ($finished || $virtual) {
                $deliverService = new GrouponTeamDeliverService();
                $deliverService->handle($team);
            } else {
                $refundService = new GrouponTeamRefundService();
                $refundService->handle($team);
            }
        }

        echo '------ end handle active team task ------' . PHP_EOL;
    }

    protected function handlePendingTeams()
    {
        $teams = $this->findPendingTeams();

        echo sprintf('pending teams: %s', $teams->count()) . PHP_EOL;

        if ($teams->count() == 0) return;

        echo '------ start handle pending team task ------' . PHP_EOL;

        foreach ($teams as $team) {
            $closeService = new GrouponTeamCloseService();
            $closeService->handle($team);
        }

        echo '------ end handle pending team task ------' . PHP_EOL;
    }

    /**
     * 查找进行中团购队伍
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|GrouponTeamModel[]
     */
    protected function findActiveTeams($limit = 100)
    {
        $status = GrouponTeamModel::STATUS_ACTIVE;

        $teams = GrouponTeamModel::query()
            ->where('target_order_count = finish_order_count')
            ->andWhere('status = :status:', ['status' => $status])
            ->limit($limit)
            ->execute();

        if ($teams->count() == 0) {
            $teams = GrouponTeamModel::query()
                ->where('expire_time < :expire_time:', ['expire_time' => time()])
                ->andWhere('status = :status:', ['status' => $status])
                ->limit($limit)
                ->execute();
        }

        return $teams;
    }

    /**
     * 查找待启动团购队伍
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|GrouponTeamModel[]
     */
    protected function findPendingTeams($limit = 100)
    {
        $status = GrouponTeamModel::STATUS_PENDING;

        return GrouponTeamModel::query()
            ->where('expire_time < :expire_time:', ['expire_time' => time()])
            ->andWhere('status = :status:', ['status' => $status])
            ->limit($limit)
            ->execute();
    }

}
