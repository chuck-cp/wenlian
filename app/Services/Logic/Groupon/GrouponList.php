<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\Groupon as GrouponModel;
use App\Repos\Groupon as GrouponRepo;
use App\Services\Logic\Service as LogicService;

class GrouponList extends LogicService
{

    use GrouponInfoTrait;

    /**
     * @var string cos存储URL
     */
    protected $cosUrl;

    public function handle()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['status'] = GrouponModel::STATUS_ACTIVE;
        $params['published'] = 1;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $grouponRepo = new GrouponRepo();

        $pager = $grouponRepo->paginate($params, $sort, $page, $limit);

        return $this->handleGroupons($pager);
    }

    protected function handleGroupons($pager)
    {
        $this->cosUrl = kg_cos_url();

        if ($pager->total_items == 0) {
            return $pager;
        }

        /**
         * @var $groupons GrouponModel[]
         */
        $groupons = $pager->items;

        $items = [];

        foreach ($groupons as $groupon) {

            $status = $this->getStatusType($groupon->start_time, $groupon->end_time);

            $item = $this->handleItemInfo($groupon->item_type, $groupon->item_info);

            $items[] = [
                'id' => $groupon->id,
                'member_price' => (float)$groupon->member_price,
                'leader_price' => (float)$groupon->leader_price,
                'partner_limit' => $groupon->partner_limit,
                'partner_expiry' => $groupon->partner_expiry,
                'start_time' => $groupon->start_time,
                'end_time' => $groupon->end_time,
                'status' => $status,
                'item' => $item,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
