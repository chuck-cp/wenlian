<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\WithdrawList as WithdrawListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Http\Admin\Services\Traits\WithdrawSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Withdraw as WithdrawModel;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Repos\Withdraw as WithdrawRepo;
use Vtiful\Kernel\Excel;

class WithdrawExport extends Service
{

    use WithdrawSearchTrait;
    use AccountSearchTrait;

    public function handle()
    {
        set_time_limit(300);

        ini_set('memory_limit', '512M');

        $pager = $this->searchWithdraws();

        if ($pager->total_items == 0) {
            return null;
        }

        $header = [
            0 => '用户编号',
            1 => '用户名称',
            2 => '提现平台',
            3 => '提现账号',
            4 => '提现金额',
            5 => '服务费',
            6 => '提现状态',
            7 => '创建时间',
        ];

        $rows = [];

        foreach ($pager->items as $item) {
            $rows[] = [
                0 => $item['user']['id'],
                1 => $item['user']['name'],
                3 => $this->getChannelTypeText($item['account']['channel']),
                2 => $item['account']['account'],
                4 => $item['apply_amount'],
                5 => $item['service_fee'],
                6 => $this->getStatusTypeText($item['status']),
                7 => date('Y-m-d H:i:s', $item['create_time']),
            ];
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('提现列表-%s.xlsx', date('Ymd'));

        $filePath = $excel->fileName($filename)->header($header)->data($rows)->output();

        kg_download($filePath);
    }

    protected function searchWithdraws()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleWithdrawSearchParams($params);
        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = 0;

        $repo = new WithdrawRepo();

        $pager = $repo->paginate($params, 'latest', 1, 10000);

        if ($pager->total_items > 0) {
            $builder = new WithdrawListBuilder();
            $items = $pager->items->toArray();
            $pipeA = $builder->handleUsers($items);
            $pipeB = $builder->handleAccounts($pipeA);
            $pager->items = $pipeB;
        }

        return $pager;
    }

    protected function getStatusTypeText($type)
    {
        $types = WithdrawModel::statusTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getChannelTypeText($type)
    {
        $types = WithdrawAccountModel::channelTypes();

        return $types[$type] ?? 'N/A';
    }

}
