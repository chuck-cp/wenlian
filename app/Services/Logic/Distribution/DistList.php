<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Distribution;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\Distribution as DistributionModel;
use App\Repos\Distribution as DistributionRepo;
use App\Services\Logic\Service as LogicService;

class DistList extends LogicService
{

    use DistInfoTrait;

    /**
     * @var string
     */
    protected $cosUrl;

    public function handle()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['published'] = 1;
        $params['deleted'] = 0;
        $params['status'] = DistributionModel::STATUS_ACTIVE;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new DistributionRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleDistributions($pager);
    }

    public function handleDistributions($pager)
    {
        $this->cosUrl = kg_cos_url();

        if ($pager->total_items == 0) {
            return $pager;
        }

        /**
         * @var $distributions DistributionModel[]
         */
        $distributions = $pager->items;

        $items = [];

        foreach ($distributions as $distribution) {

            $status = $this->getStatusType($distribution->start_time, $distribution->end_time);

            $item = $this->handleItemInfo($distribution->item_type, $distribution->item_info);

            $comAmount = round($distribution->v1_com_rate * $item['price'] / 100, 2);

            $items[] = [
                'id' => $distribution->id,
                'com_amount' => $comAmount,
                'status' => $status,
                'item' => $item,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
