<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class WithdrawAccount extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(WithdrawAccountModel::class);

        $builder->where('1 = 1');

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['order_id'])) {
            $builder->andWhere('order_id = :order_id:', ['order_id' => $where['order_id']]);
        }

        if (!empty($where['account'])) {
            $builder->andWhere('account = :account:', ['account' => $where['account']]);
        }

        if (!empty($where['channel'])) {
            $builder->andWhere('channel = :channel:', ['channel' => $where['channel']]);
        }

        if (isset($where['verified'])) {
            $builder->andWhere('verified = :verified:', ['verified' => $where['verified']]);
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
     * @return WithdrawAccountModel|Model|bool
     */
    public function findById($id)
    {
        return WithdrawAccountModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $userId
     * @param string $channelAccount
     * @param int $channelType
     * @return WithdrawAccountModel|Model|bool
     */
    public function findByUserChannelAccount($userId, $channelAccount, $channelType)
    {
        return WithdrawAccountModel::findFirst([
            'conditions' => 'user_id = ?1 AND account = ?2 AND channel = ?3',
            'bind' => [1 => $userId, 2 => $channelAccount, 3 => $channelType],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param array $ids
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|WithdrawAccountModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return WithdrawAccountModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

}
