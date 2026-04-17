<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Caches;

use App\Services\Logic\FlashSale\SaleList;

class IndexFlashSaleList extends Cache
{

    protected $lifetime = 3600;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return 'index_flash_sale_list';
    }

    public function getContent($id = null)
    {
        $service = new SaleList();

        $sales = $service->handle();

        return $sales[0]['items'] ?? [];
    }

}
