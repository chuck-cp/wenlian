<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\InvoiceList as InvoiceListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Invoice as InvoiceModel;
use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Repos\Invoice as InvoiceRepo;
use Vtiful\Kernel\Excel;

class InvoiceExport extends Service
{

    use AccountSearchTrait;

    public function handle()
    {
        set_time_limit(300);

        ini_set('memory_limit', '512M');

        $invoices = $this->searchInvoices();

        if (count($invoices) == 0) {
            return null;
        }

        $header = [
            0 => '抬头类型',
            1 => '发票类型',
            2 => '发票金额',
            3 => '发票抬头',
            4 => '纳税识别号',
            5 => '开户银行',
            6 => '银行账户',
            7 => '企业地址',
            8 => '企业电话',
            9 => '开票状态',
            10 => '创建时间',
        ];

        $rows = [];

        foreach ($invoices as $invoice) {
            $rows[] = [
                0 => $this->getHeadTypeText($invoice['account']['head_type']),
                1 => $this->getUsageTypeText($invoice['account']['usage_type']),
                2 => $invoice['amount'],
                3 => $invoice['account']['head_name'] ?: 'N/A',
                4 => $invoice['account']['tax_account'] ?: 'N/A',
                5 => $invoice['account']['bank_name'] ?: 'N/A',
                6 => $invoice['account']['bank_account'] ?: 'N/A',
                7 => $invoice['account']['company_address'] ?: 'N/A',
                8 => $invoice['account']['company_phone'] ?: 'N/A',
                9 => $this->getStatusTypeText($invoice['status']),
                10 => date('Y-m-d H:i:s', $invoice['create_time']),
            ];
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('开票申请表-%s.xlsx', date('Ymd'));

        $filePath = $excel->fileName($filename)->header($header)->data($rows)->output();

        kg_download($filePath);
    }

    protected function searchInvoices()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = 0;

        $repo = new InvoiceRepo();

        $pager = $repo->paginate($params, 'latest', 1, 10000);

        return $this->handleInvoices($pager);
    }

    protected function handleInvoices($pager)
    {
        $invoices = [];

        if ($pager->total_items > 0) {

            $builder = new InvoiceListBuilder();

            $pipeA = $pager->items->toArray();

            $invoices = $builder->handleAccounts($pipeA);
        }

        return $invoices;
    }

    protected function getHeadTypeText($type)
    {
        $types = InvoiceAccountModel::headTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getUsageTypeText($type)
    {
        $types = InvoiceAccountModel::usageTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getStatusTypeText($type)
    {
        $types = InvoiceModel::statusTypes();

        return $types[$type] ?? 'N/A';
    }

}
