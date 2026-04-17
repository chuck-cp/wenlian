<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\FlashSale\OrderCreate as OrderCreateService;
use App\Services\Logic\FlashSale\SaleList as SaleListService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;

/**
 * @RoutePrefix("/flash/sale")
 */
class FlashSaleController extends Controller
{

    /**
     * @Get("/", name="home.flash_sale.index")
     */
    public function indexAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getFlashSaleListUrl();
            return $this->response->redirect($location);
        }

        $this->seo->prependTitle('秒杀');

        $service = new SaleListService();

        $sales = $service->handle();

        $this->view->setVar('sales', $sales);
    }

    /**
     * @Post("/{id:[0-9]+}/order", name="home.flash_sale.order")
     */
    public function orderAction($id)
    {
        $service = new OrderCreateService();

        $order = $service->run($id);

        $location = $this->url->get(
            ['for' => 'home.order.pay'],
            ['sn' => $order->sn]
        );

        return $this->jsonSuccess(['location' => $location]);
    }

}
