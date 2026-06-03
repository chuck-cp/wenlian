<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\DigitalCard as DigitalCardModel;
use App\Repos\DigitalCard as DigitalCardRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;

class DigitalRedeemList extends LogicService
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

        $repo = new DigitalCardRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    public function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $items = [];

        /**
         * @var DigitalCardModel[] $cards
         */
        $cards = $pager->items;

        foreach ($cards as $card) {
            $items[] = [
                'id' => $card->id,
                'code' => $card->code,
                'deleted' => $card->deleted,
                'redeem_time' => $card->redeem_time,
                'create_time' => $card->create_time,
                'update_time' => $card->update_time,
                'user' => [
                    'id' => $card->user_id,
                    'name' => $card->user_name,
                ],
                'item' => [
                    'id' => $card->item_id,
                    'title' => $card->item_title,
                    'price' => $card->item_price,
                    'type' => $card->item_type,
                ],
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
