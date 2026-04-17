<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Invoice\AccountCreate as InvoiceAccountCreateService;
use App\Services\Logic\Invoice\AccountDelete as InvoiceAccountDeleteService;
use App\Services\Logic\Invoice\AccountInfo as InvoiceAccountInfoService;

/**
 * @RoutePrefix("/api/invoice/account")
 */
class InvoiceAccountController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.invoice_account.info")
     */
    public function infoAction($id)
    {
        $service = new InvoiceAccountInfoService();

        $account = $service->handle($id);

        return $this->jsonSuccess(['account' => $account]);
    }

    /**
     * @Post("/create", name="api.invoice_account.create")
     */
    public function createAction()
    {
        $service = new InvoiceAccountCreateService();

        $account = $service->handle();

        $service = new InvoiceAccountInfoService();

        $account = $service->handle($account->id);

        return $this->jsonSuccess(['account' => $account]);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="api.invoice_account.delete")
     */
    public function deleteAction($id)
    {
        $service = new InvoiceAccountDeleteService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

}
