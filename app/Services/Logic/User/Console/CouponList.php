<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Builders\CouponUserList as CouponUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\CouponUser as CouponUserRepo;
use App\Services\Logic\Service as LogicService;

class CouponList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new CouponUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleCoupons($pager);
    }

    public function handleCoupons($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new CouponUserListBuilder();

        $relations = $pager->items->toArray();

        $coupons = $builder->getCoupons($relations);

        $items = [];

        foreach($relations as $relation)
        {
            $coupon = $coupons[$relation['coupon_id']] ?? new \stdClass();

            $items[] = [
                'channel' => $relation['channel'],
                'apply_count' => $relation['apply_count'],
                'create_time' => $relation['create_time'],
                'update_time' => $relation['update_time'],
                'coupon' => $coupon,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
