<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Invoice\InvoiceCancel as InvoiceCancelService;
use App\Services\Logic\Invoice\InvoiceCreate as InvoiceCreateService;

/**
 * @RoutePrefix("/invoice")
 */
class InvoiceController extends Controller
{

    /**
     * @Post("/create", name="home.invoice.create")
     */
    public function createAction()
    {
        $service = new InvoiceCreateService();

        $service->handle();

        $location = $this->url->get(
            ['for' => 'home.uc.invoice'],
            ['action' => 'list']
        );

        $content = [
            'location' => $location,
            'msg' => '申请开票成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/cancel", name="home.invoice.cancel")
     */
    public function cancelAction($id)
    {
        $service = new InvoiceCancelService();

        $service->handle($id);

        $location = $this->url->get(
            ['for' => 'home.uc.invoice'],
            ['action' => 'list']
        );

        $content = [
            'location' => $location,
            'msg' => '取消开票成功',
        ];

        return $this->jsonSuccess($content);
    }

}
