<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Repos\CashHistory as CashHistoryRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;

class CashHistory extends LogicService
{

    use UserTrait;

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $historyRepo = new CashHistoryRepo();

        $pager = $historyRepo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    public function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $items = [];

        foreach ($pager->items as $item) {
            $items[] = [
                'id' => $item->id,
                'event_id' => $item->event_id,
                'event_type' => $item->event_type,
                'event_info' => $item->event_info,
                'event_amount' => $item->event_amount,
                'create_time' => $item->create_time,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
