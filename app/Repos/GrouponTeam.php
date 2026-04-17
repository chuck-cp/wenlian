<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\GrouponTeamUser as GrouponTeamUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class GrouponTeam extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(GrouponTeamModel::class);

        $builder->where('1 = 1');

        if (!empty($where['groupon_id'])) {
            $builder->andWhere('groupon_id = :groupon_id:', ['groupon_id' => $where['groupon_id']]);
        }

        if (!empty($where['leader_id'])) {
            $builder->andWhere('leader_id = :leader_id:', ['leader_id' => $where['leader_id']]);
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (!empty($where['status'])) {
            if (is_array($where['status'])) {
                $builder->inWhere('status', $where['status']);
            } else {
                $builder->andWhere('status = :status:', ['status' => $where['status']]);
            }
        }

        switch ($sort) {
            case 'oldest':
                $orderBy = 'id ASC';
                break;
            default:
                $orderBy = 'id DESC';
                break;
        }

        $builder->orderBy($orderBy);

        $pager = new PagerQueryBuilder([
            'builder' => $builder,
            'page' => $page,
            'limit' => $limit,
        ]);

        return $pager->paginate();
    }

    /**
     * @param int $id
     * @return GrouponTeamModel|Model|bool
     */
    public function findById($id)
    {
        return GrouponTeamModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $grouponId
     * @param int $userId
     * @return GrouponTeamModel|Model|bool
     */
    public function findPendingGrouponTeam($grouponId, $userId)
    {
        $status = GrouponTeamModel::STATUS_PENDING;

        return $this->findStatusGrouponTeam($grouponId, $userId, $status);
    }

    /**
     * @param int $grouponId
     * @param int $userId
     * @return GrouponTeamModel|Model|bool
     */
    public function findActiveGrouponTeam($grouponId, $userId)
    {
        $status = GrouponTeamModel::STATUS_ACTIVE;

        return $this->findStatusGrouponTeam($grouponId, $userId, $status);
    }

    /**
     * @param int $grouponId
     * @param int $userId
     * @return GrouponTeamModel|Model|bool
     */
    public function findFinishedGrouponTeam($grouponId, $userId)
    {
        $status = GrouponTeamModel::STATUS_FINISHED;

        return $this->findStatusGrouponTeam($grouponId, $userId, $status);
    }

    /**
     * @param int $grouponId
     * @param int $userId
     * @param int $status
     * @return GrouponTeamModel|Model|bool
     */
    public function findStatusGrouponTeam($grouponId, $userId, $status)
    {
        return GrouponTeamModel::findFirst([
            'conditions' => 'groupon_id = ?1 AND leader_id = ?2 AND status = ?3',
            'bind' => [1 => $grouponId, 2 => $userId, 3 => $status],
            'order' => 'id DESC',
        ]);
    }

    /**
     *
     * @param int $teamId
     * @return ResultsetInterface|Resultset|GrouponTeamUserModel[]
     */
    public function findPendingTeamUsers($teamId)
    {
        $status = GrouponTeamUserModel::STATUS_PENDING;

        return GrouponTeamUserModel::query()
            ->where('team_id = :team_id:', ['team_id' => $teamId])
            ->andWhere('status = :status:', ['status' => $status])
            ->execute();
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
