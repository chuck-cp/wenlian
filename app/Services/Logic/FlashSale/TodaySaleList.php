<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\FlashSale;

use App\Services\Logic\Service as LogicService;

class TodaySaleList extends LogicService
{

    public function handle()
    {
        $service = new SaleList();

        $sales = $service->handle();

        $result = [];

        if (empty($sales)) return $result;

        $today = date('m / d');

        foreach ($sales as $sale) {
            if ($sale['date'] == $today) {
                $result = $sale['items'];
            }
        }

        return $result;
    }

}
