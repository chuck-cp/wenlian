<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Coupon;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\Coupon as CouponModel;
use App\Models\User as UserModel;
use App\Repos\Coupon as CouponRepo;
use App\Repos\CouponUser as CouponUserRepo;
use App\Services\Logic\Service as LogicService;

class CouponList extends LogicService
{

    public function handle()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['private'] = 0;
        $params['published'] = 1;
        $params['status'] = CouponModel::STATUS_ACTIVE;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $couponRepo = new CouponRepo();

        $pager = $couponRepo->paginate($params, $sort, $page, $limit);

        return $this->handleCoupons($pager);
    }

    public function handleCoupons($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $user = $this->getCurrentUser(true);

        $myCouponIds = $this->getMyCouponIds($user);

        /**
         * @var $coupons CouponModel[]
         */
        $coupons = $pager->items;

        $items = [];

        foreach ($coupons as $coupon) {

            $me = ['claimed' => 0];

            if (in_array($coupon->id, $myCouponIds)) {
                $me['claimed'] = 1;
            }

            $items[] = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'attrs' => $coupon->attrs,
                'consume_limit' => $coupon->consume_limit,
                'total_usage' => $coupon->total_usage,
                'user_usage' => $coupon->user_usage,
                'item_id' => $coupon->item_id,
                'item_type' => $coupon->item_type,
                'item_info' => $coupon->item_info,
                'start_time' => $coupon->start_time,
                'end_time' => $coupon->end_time,
                'me' => $me,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

    protected function getMyCouponIds(UserModel $user)
    {
        if ($user->id == 0) return [];

        $repo = new CouponUserRepo();

        $where = ['user_id' => $user->id];

        $pager = $repo->paginate($where, 'latest', 1, 1000);

        $result = [];

        if ($pager->total_items > 0) {
            foreach ($pager->items as $item) {
                $result[] = $item->coupon_id;
            }
        }

        return $result;
    }

}
