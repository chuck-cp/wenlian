<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Withdraw as WithdrawModel;
use App\Models\WithdrawStatus as WithdrawStatusModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Withdraw extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(WithdrawModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['sn'])) {
            $builder->andWhere('sn = :sn:', ['sn' => $where['sn']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['account_id'])) {
            $builder->andWhere('account_id = :account_id:', ['account_id' => $where['account_id']]);
        }

        if (!empty($where['status'])) {
            if (is_array($where['status'])) {
                $builder->inWhere('status', $where['status']);
            } else {
                $builder->andWhere('status = :status:', ['status' => $where['status']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
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
     * @return WithdrawModel|Model|bool
     */
    public function findById($id)
    {
        return WithdrawModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $sn
     * @return WithdrawModel|Model|bool
     */
    public function findBySn($sn)
    {
        return WithdrawModel::findFirst([
            'conditions' => 'sn = :sn:',
            'bind' => ['sn' => $sn],
        ]);
    }

    /**
     * @param int $userId
     * @return WithdrawModel|Model|bool
     */
    public function findUserLastWithdraw($userId)
    {
        return WithdrawModel::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userId],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $withdrawId
     * @return ResultsetInterface|Resultset|WithdrawStatusModel[]
     */
    public function findStatusHistory($withdrawId)
    {
        return WithdrawStatusModel::query()
            ->where('withdraw_id = :withdraw_id:', ['withdraw_id' => $withdrawId])
            ->execute();
    }

    /**
     * @param int $userId
     * @return int
     */
    public function countUserMonthlyWithdraws($userId)
    {
        $status = WithdrawModel::STATUS_FINISHED;

        $time = strtotime(date('Y-m'));

        return (int)WithdrawModel::count([
            'conditions' => 'user_id = ?1 AND status = ?2 AND create_time > ?3',
            'bind' => [1 => $userId, 2 => $status, 3 => $time],
        ]);
    }

}
