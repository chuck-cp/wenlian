<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Services\Logic\Service as LogicService;

class WithdrawAccountList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['verified'] = 1;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new WithdrawAccountRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleWithdrawAccounts($pager);
    }

    public function handleWithdrawAccounts($pager)
    {
        $items = [];

        if ($pager->total_items == 0) {
            return $items;
        }

        $channels = WithdrawAccountModel::getEnabledChannels();

        /**
         * @var $accounts WithdrawAccountModel[]
         */
        $accounts = $pager->items;

        foreach ($accounts as $account) {

            $disabled = array_key_exists($account->channel, $channels) ? 0 : 1;

            $items[] = [
                'id' => $account->id,
                'channel' => $account->channel,
                'name' => $account->name,
                'account' => $account->account,
                'master' => $account->master,
                'verified' => $account->verified,
                'create_time' => $account->create_time,
                'update_time' => $account->update_time,
                'disabled' => $disabled,
            ];
        }

        return $items;
    }

}
