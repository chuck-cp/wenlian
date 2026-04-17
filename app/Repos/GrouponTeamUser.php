<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\GrouponTeamUser as GrouponTeamUserModel;
use Phalcon\Mvc\Model;

class GrouponTeamUser extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(GrouponTeamUserModel::class);

        $builder->where('1 = 1');

        if (!empty($where['groupon_id'])) {
            $builder->andWhere('groupon_id = :groupon_id:', ['groupon_id' => $where['groupon_id']]);
        }

        if (!empty($where['team_id'])) {
            $builder->andWhere('team_id = :team_id:', ['team_id' => $where['team_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
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
     * @param int $orderId
     * @return GrouponTeamUserModel|Model|bool
     */
    public function findByOrderId($orderId)
    {
        return GrouponTeamUserModel::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId],
        ]);
    }

    /**
     * @param int $grouponId
     * @param int $userId
     * @return GrouponTeamUserModel|Model|bool
     */
    public function findFinishedGrouponUser($grouponId, $userId)
    {
        $status = GrouponTeamUserModel::STATUS_FINISHED;

        return GrouponTeamUserModel::findFirst([
            'conditions' => 'groupon_id = ?1 AND user_id = ?2 AND status = ?3',
            'bind' => [1 => $grouponId, 2 => $userId, 3 => $status],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $teamId
     * @param int $userId
     * @return GrouponTeamUserModel|Model|bool
     */
    public function findPendingTeamUser($teamId, $userId)
    {
        $status = GrouponTeamUserModel::STATUS_PENDING;

        return GrouponTeamUserModel::findFirst([
            'conditions' => 'team_id = ?1 AND user_id = ?2 AND status = ?3',
            'bind' => [1 => $teamId, 2 => $userId, 3 => $status],
            'order' => 'id DESC',
        ]);
    }

}
