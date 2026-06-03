<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\Coupon as CouponRepo;

class CouponUserList extends Builder
{

    public function handleCoupons(array $relations)
    {
        $coupons = $this->getCoupons($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['coupon'] = $coupons[$value['coupon_id']] ?? null;
        }

        return $relations;
    }

    public function handleUsers(array $relations)
    {
        $users = $this->getUsers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['user'] = $users[$value['user_id']] ?? null;
        }

        return $relations;
    }

    public function getCoupons(array $relations)
    {
        $ids = kg_array_column($relations, 'coupon_id');

        $couponRepo = new CouponRepo();

        $columns = [
            'id', 'code', 'name', 'type', 'attrs',
            'consume_limit', 'total_usage', 'user_usage',
            'item_id', 'item_type', 'item_info', 'start_time', 'end_time',
        ];

        $coupons = $couponRepo->findByIds($ids, $columns);

        $result = [];

        foreach ($coupons->toArray() as $coupon) {
            $coupon['attrs'] = json_decode($coupon['attrs'], true);
            $coupon['item_info'] = json_decode($coupon['item_info'], true);
            $result[$coupon['id']] = $coupon;
        }

        return $result;
    }

    public function getUsers(array $relations)
    {
        $ids = kg_array_column($relations, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
