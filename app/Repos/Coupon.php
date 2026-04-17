<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Coupon as CouponModel;
use App\Models\CouponUser as CouponUserModel;
use App\Models\Order as OrderModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Coupon extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(CouponModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['code'])) {
            $builder->andWhere('code = :code:', ['code' => $where['code']]);
        }

        if (!empty($where['name'])) {
            $builder->andWhere('name LIKE :name:', ['name' => '%' . $where['name'] . '%']);
        }

        if (!empty($where['type'])) {
            if (is_array($where['type'])) {
                $builder->inWhere('type', $where['type']);
            } else {
                $builder->andWhere('type = :type:', ['type' => $where['type']]);
            }
        }

        if (!empty($where['item_type'])) {
            if (is_array($where['item_type'])) {
                $builder->inWhere('item_type', $where['item_type']);
            } else {
                $builder->andWhere('item_type = :item_type:', ['item_type' => $where['item_type']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (!empty($where['status'])) {
            if ($where['status'] == CouponModel::STATUS_PENDING) {
                $builder->andWhere('start_time > :start_time:', ['start_time' => time()]);
            } elseif ($where['status'] == CouponModel::STATUS_ACTIVE) {
                $builder->andWhere('total_usage > apply_count');
                $builder->andWhere('start_time < :start_time:', ['start_time' => time()]);
                $builder->andWhere('end_time > :end_time:', ['end_time' => time()]);
            } elseif ($where['status'] == CouponModel::STATUS_EXPIRED) {
                $builder->andWhere('end_time < :end_time:', ['end_time' => time()]);
            }
        }

        if (isset($where['private'])) {
            $builder->andWhere('private = :private:', ['private' => $where['private']]);
        }

        if (isset($where['published'])) {
            $builder->andWhere('published = :published:', ['published' => $where['published']]);
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
     * @param string $code
     * @return CouponModel|Model|bool
     */
    public function findByCode($code)
    {
        return CouponModel::findFirst([
            'conditions' => 'code = :code:',
            'bind' => ['code' => $code],
        ]);
    }

    /**
     * @param int $id
     * @return CouponModel|Model|bool
     */
    public function findById($id)
    {
        return CouponModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param array $ids
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|CouponModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return CouponModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

    /**
     * @param int $itemId
     * @param int $itemType
     * @return CouponModel|Model|bool
     */
    public function findItemCoupon($itemId, $itemType)
    {
        return CouponModel::findFirst([
            'conditions' => 'item_id = ?1 AND item_type = ?2 AND deleted = 0',
            'bind' => [1 => $itemId, 2 => $itemType],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $couponId
     * @return int
     */
    public function countClaimedUsers($couponId)
    {
        return (int)CouponUserModel::count([
            'conditions' => 'coupon_id = :coupon_id:',
            'bind' => ['coupon_id' => $couponId],
        ]);
    }

    /**
     * @param int $couponId
     * @return int
     */
    public function countAppliedOrders($couponId)
    {
        return (int)OrderModel::count([
            'conditions' => 'promotion_id = ?1 AND promotion_type = ?2',
            'bind' => [1 => $couponId, 2 => OrderModel::PROMOTION_COUPON],
        ]);
    }

}
