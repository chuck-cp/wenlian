<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\CouponUser as CouponUserModel;
use App\Models\Order as OrderModel;
use Phalcon\Mvc\Model;

class CouponUser extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(CouponUserModel::class);

        $builder->where('1 = 1');

        if (!empty($where['coupon_id'])) {
            $builder->andWhere('coupon_id = :coupon_id:', ['coupon_id' => $where['coupon_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['channel'])) {
            $builder->andWhere('channel = :channel:', ['channel' => $where['channel']]);
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
     * @param int $couponId
     * @param int $userId
     * @return CouponUserModel|Model|bool
     */
    public function findCouponUser($couponId, $userId)
    {
        return CouponUserModel::findFirst([
            'conditions' => 'coupon_id = ?1 AND user_id = ?2',
            'bind' => [1 => $couponId, 2 => $userId],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $couponId
     * @param int $userId
     * @return int
     */
    public function countAppliedOrders($couponId, $userId)
    {
        return (int)OrderModel::count([
            'conditions' => 'owner_id = ?1 AND promotion_id = ?2 AND promotion_type = ?3',
            'bind' => [1 => $userId, 2 => $couponId, 3 => OrderModel::PROMOTION_COUPON],
        ]);
    }

    public function deleteByCouponId($couponId)
    {
        $phql = sprintf('UPDATE %s SET deleted = 1 WHERE coupon_id = :coupon_id:', CouponUserModel::class);

        return $this->modelsManager->executeQuery($phql, ['coupon_id' => $couponId]);
    }

    public function restoreByCouponId($couponId)
    {
        $phql = sprintf('UPDATE %s SET deleted = 0 WHERE coupon_id = :coupon_id:', CouponUserModel::class);

        return $this->modelsManager->executeQuery($phql, ['coupon_id' => $couponId]);
    }

}
