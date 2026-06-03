<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Http\Admin\Services\Traits\DigitalCardSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\DigitalCard as DigitalCardModel;
use App\Repos\DigitalCard as DigitalCardRepo;
use Vtiful\Kernel\Excel;

class DigitalCardExport extends Service
{

    use DigitalCardSearchTrait;
    use AccountSearchTrait;

    public function handle()
    {
        set_time_limit(300);

        ini_set('memory_limit', '512M');

        $pager = $this->searchDigitalCards();

        if ($pager->total_items == 0) {
            return null;
        }

        /**
         * @var $items DigitalCardModel[]
         */
        $items = $pager->items;

        $header = [
            0 => '兑换码',
            1 => '商品编号',
            2 => '商品名称',
            3 => '商品类型',
            4 => '用户名称',
            5 => '用户编号',
            6 => '兑换时间',
            7 => '过期时间',
        ];

        $rows = [];

        foreach ($items as $item) {
            $rows[] = [
                0 => $item->code,
                1 => $item->item_id,
                2 => $item->item_title,
                3 => $this->getItemTypeText($item->item_type),
                4 => $this->getUserNameText($item->user_name),
                5 => $this->getUserIdText($item->user_id),
                6 => $this->getRedeemTimeText($item->redeem_time),
                7 => $this->getExpireTimeText($item->expire_time),
            ];
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('电子兑换码-%s.xlsx', date('Ymd'));

        $filePath = $excel->fileName($filename)->header($header)->data($rows)->output();

        kg_download($filePath);
    }

    protected function searchDigitalCards()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleDigitalCardSearchParams($params);
        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = 0;

        $repo = new DigitalCardRepo();

        return $repo->paginate($params, 'latest', 1, 10000);
    }

    protected function getItemTypeText($type)
    {
        $types = DigitalCardModel::itemTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getUserIdText($id)
    {
        return $id > 0 ? $id : 'N/A';
    }

    protected function getUserNameText($name)
    {
        return !empty($name) ? $name : 'N/A';
    }

    protected function getExpireTimeText($time)
    {
        return $time > 0 ? date('Y-m-d H:i:s', $time) : 'N/A';
    }

    protected function getRedeemTimeText($time)
    {
        return $time > 0 ? date('Y-m-d H:i:s', $time) : 'N/A';
    }

}
