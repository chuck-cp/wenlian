<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Builders\InvoiceList as InvoiceListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Invoice as InvoiceRepo;
use App\Services\Logic\Service as LogicService;

class InvoiceList extends LogicService
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

        $repo = new InvoiceRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleInvoices($pager);
    }

    public function handleInvoices($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new InvoiceListBuilder();

        $invoices = $pager->items->toArray();

        $accounts = $builder->getAccounts($invoices);
        $contacts = $builder->getContacts($invoices);

        $items = [];

        $baseUrl = kg_cos_url();

        foreach ($invoices as $invoice) {

            if (!empty($invoice['voucher'])) {
                $invoice['voucher'] = $baseUrl . $invoice['voucher'];
            }

            $account = $accounts[$invoice['account_id']] ?? new \stdClass();
            $contact = $contacts[$invoice['contact_id']] ?? new \stdClass();

            $me = $builder->handleMeInfo($invoice);

            $items[] = [
                'id' => $invoice['id'],
                'status' => $invoice['status'],
                'amount' => (float)$invoice['amount'],
                'voucher' => $invoice['voucher'],
                'media_type' => $invoice['media_type'],
                'sort_no' => $invoice['sort_no'],
                'serial_no' => $invoice['serial_no'],
                'post_email' => $invoice['post_email'],
                'create_time' => $invoice['create_time'],
                'update_time' => $invoice['update_time'],
                'account' => $account,
                'contact' => $contact,
                'me' => $me,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
