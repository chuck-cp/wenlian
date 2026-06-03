<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Builders\WithdrawList as WithdrawListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Withdraw as WithdrawRepo;
use App\Services\Logic\Service as LogicService;

class WithdrawList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new WithdrawRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleWithdraws($pager);
    }

    public function handleWithdraws($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new WithdrawListBuilder();

        $withdraws = $pager->items->toArray();

        $accounts = $builder->getAccounts($withdraws);

        $items = [];

        foreach ($withdraws as $withdraw) {

            $account = $accounts[$withdraw['account_id']] ?? new \stdClass();

            $me = $builder->handleMeInfo($withdraw);

            $items[] = [
                'id' => $withdraw['id'],
                'sn' => $withdraw['sn'],
                'status' => $withdraw['status'],
                'apply_amount' => (float)$withdraw['apply_amount'],
                'trans_amount' => (float)$withdraw['trans_amount'],
                'service_fee' => (float)$withdraw['service_fee'],
                'tax_fee' => (float)$withdraw['tax_fee'],
                'create_time' => $withdraw['create_time'],
                'update_time' => $withdraw['update_time'],
                'account' => $account,
                'me' => $me,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
