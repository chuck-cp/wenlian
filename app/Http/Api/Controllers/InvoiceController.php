<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Invoice\InvoiceCancel as InvoiceCancelService;
use App\Services\Logic\Invoice\InvoiceCreate as InvoiceCreateService;
use App\Services\Logic\Invoice\InvoiceInfo as InvoiceInfoService;

/**
 * @RoutePrefix("/api/invoice")
 */
class InvoiceController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.invoice.info")
     */
    public function infoAction($id)
    {
        $service = new InvoiceInfoService();

        $invoice = $service->handle($id);

        return $this->jsonSuccess(['invoice' => $invoice]);
    }

    /**
     * @Post("/create", name="api.invoice.create")
     */
    public function createAction()
    {
        $service = new InvoiceCreateService();

        $invoice = $service->handle();

        $service = new InvoiceInfoService();

        $invoice = $service->handle($invoice->id);

        return $this->jsonSuccess(['invoice' => $invoice]);
    }

    /**
     * @Post("/{id:[0-9]+}/cancel", name="api.invoice.cancel")
     */
    public function cancelAction($id)
    {
        $service = new InvoiceCancelService();

        $invoice = $service->handle($id);

        $service = new InvoiceInfoService();

        $invoice = $service->handle($invoice->id);

        return $this->jsonSuccess(['invoice' => $invoice]);
    }

}
