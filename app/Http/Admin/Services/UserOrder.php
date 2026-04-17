<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\OrderList as OrderListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Order as OrderRepo;
use App\Validators\User as UserValidator;

class UserOrder extends Service
{

    public function getOrders($id)
    {
        $user = $this->findUserOrFail($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['owner_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $orderRepo = new OrderRepo();

        $pager = $orderRepo->paginate($params, $sort, $page, $limit);

        return $this->handleOrders($pager);
    }

    protected function handleOrders($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new OrderListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleItems($pipeA);
            $pipeC = $builder->objects($pipeB);

            $pager->items = $pipeC;
        }

        return $pager;
    }

    protected function findUserOrFail($id)
    {
        $validator = new UserValidator();

        return $validator->checkUser($id);
    }

}
