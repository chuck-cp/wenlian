<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\FlashSale\OrderCreate as FlashOrderCreateService;
use App\Services\Logic\FlashSale\SaleInfo as FlashSaleInfoService;
use App\Services\Logic\FlashSale\SaleList as FlashSaleListService;
use App\Services\Logic\FlashSale\TodaySaleList as TodayFlashSaleListService;
use App\Services\Logic\Order\OrderInfo as OrderInfoService;

/**
 * @RoutePrefix("/api/flash/sale")
 */
class FlashSaleController extends Controller
{

    /**
     * @Get("/list", name="api.flash_sale.list")
     */
    public function listAction()
    {
        $service = new FlashSaleListService();

        $sales = $service->handle();

        return $this->jsonSuccess(['sales' => $sales]);
    }

    /**
     * @Get("/list/today", name="api.flash_sale.today_list")
     */
    public function todayListAction()
    {
        $service = new TodayFlashSaleListService();

        $sales = $service->handle();

        return $this->jsonSuccess(['sales' => $sales]);
    }

    /**
     * @Get("/{id:[0-9]+}/info", name="api.flash_sale.info")
     */
    public function infoAction($id)
    {
        $service = new FlashSaleInfoService();

        $sale = $service->handle($id);

        if ($sale['deleted'] == 1) {
            $this->notFound();
        }

        if ($sale['published'] == 0) {
            $this->notFound();
        }

        return $this->jsonSuccess(['sale' => $sale]);
    }

    /**
     * @Post("/{id:[0-9]+}/order", name="api.flash_sale.order")
     */
    public function orderAction($id)
    {
        $service = new FlashOrderCreateService();

        $order = $service->run($id);

        $service = new OrderInfoService();

        $order = $service->handle($order->sn);

        return $this->jsonSuccess(['order' => $order]);
    }

}
