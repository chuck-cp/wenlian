<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\OrderList as OrderListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Http\Admin\Services\Traits\OrderSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Order as OrderModel;
use App\Repos\Order as OrderRepo;
use Vtiful\Kernel\Excel;

class OrderExport extends Service
{

    use OrderSearchTrait;
    use AccountSearchTrait;

    public function handle()
    {
        set_time_limit(300);

        ini_set('memory_limit', '512M');

        return $this->exportOrders();
    }

    protected function exportOrders()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleOrderSearchParams($params);
        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = 0;

        $orderRepo = new OrderRepo();

        $pager = $orderRepo->paginate($params);

        if ($pager->total_items == 0) {
            return null;
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('订单列表-%s.xlsx', date('Ymd'));

        $excel = $excel->fileName($filename);

        $limit = 10000;

        $totalPage = ceil($pager->total_items / $limit);

        $builder = new OrderListBuilder();

        for ($page = 1; $page <= $totalPage; $page++) {

            $myPager = $orderRepo->paginate($params, 'latest', $page, $limit);

            $items = $myPager->items->toArray();

            $items = $builder->handleUsers($items);

            $data = [];

            foreach ($items as $item) {
                $data[] = $this->handleExcelRow($item);
            }

            $header = $this->getExcelHeader();

            $excel = $excel->header($header);

            if ($page > 1) {
                $excel = $excel->addSheet("Sheet{$page}");
            }

            $excel = $excel->data($data);
        }

        $filePath = $excel->output();

        kg_download($filePath);
    }

    protected function getExcelHeader()
    {
        return [
            0 => '订单编号',
            1 => '用户编号',
            2 => '用户名称',
            3 => '商品类型',
            4 => '商品名称',
            5 => '订单金额',
            6 => '促销类型',
            7 => '订单状态',
            8 => '创建时间',
        ];
    }

    protected function handleExcelRow($item)
    {
        return [
            0 => $item['sn'],
            1 => $item['owner']['id'],
            2 => $item['owner']['name'],
            3 => $this->getItemTypeText($item['item_type']),
            4 => $item['subject'],
            5 => $item['amount'],
            6 => $this->getPromotionTypeText($item['promotion_type']),
            7 => $this->getStatusTypeText($item['status']),
            8 => date('Y-m-d H:i:s', $item['create_time']),
        ];
    }

    protected function getItemTypeText($type)
    {
        $types = OrderModel::itemTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getStatusTypeText($type)
    {
        $types = OrderModel::statusTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getPromotionTypeText($type)
    {
        $types = OrderModel::promotionTypes();

        return $types[$type] ?? 'N/A';
    }

}
