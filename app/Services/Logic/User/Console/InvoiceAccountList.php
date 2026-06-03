<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;
use App\Services\Logic\Service as LogicService;

class InvoiceAccountList extends LogicService
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

        $repo = new InvoiceAccountRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleInvoiceAccounts($pager);
    }

    public function handleInvoiceAccounts($pager)
    {
        $items = [];

        if ($pager->total_items == 0) {
            return $items;
        }

        $usageTypes = InvoiceAccountModel::getEnabledUsageTypes();

        /**
         * @var $accounts InvoiceAccountModel[]
         */
        $accounts = $pager->items;

        foreach ($accounts as $account) {

            $disabled = array_key_exists($account->usage_type, $usageTypes) ? 0 : 1;

            $items[] = [
                'id' => $account->id,
                'head_type' => $account->head_type,
                'head_name' => $account->head_name,
                'usage_type' => $account->usage_type,
                'tax_account' => $account->tax_account,
                'bank_name' => $account->bank_name,
                'bank_account' => $account->bank_account,
                'company_phone' => $account->company_phone,
                'company_address' => $account->company_address,
                'create_time' => $account->create_time,
                'update_time' => $account->update_time,
                'disabled' => $disabled,
            ];
        }

        return $items;
    }

}
