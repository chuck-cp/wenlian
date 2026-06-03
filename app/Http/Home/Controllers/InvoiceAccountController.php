<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Invoice\AccountCreate as InvoiceAccountCreateService;
use App\Services\Logic\Invoice\AccountDelete as InvoiceAccountDeleteService;

/**
 * @RoutePrefix("/invoice/account")
 */
class InvoiceAccountController extends Controller
{

    /**
     * @Post("/create", name="home.invoice_account.create")
     */
    public function createAction()
    {
        $service = new InvoiceAccountCreateService();

        $service->handle();

        $location = $this->url->get(
            ['for' => 'home.uc.invoice'],
            ['action' => 'apply']
        );

        $content = [
            'location' => $location,
            'msg' => '添加抬头成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="home.invoice_account.delete")
     */
    public function deleteAction($id)
    {
        $service = new InvoiceAccountDeleteService();

        $service->handle($id);

        return $this->jsonSuccess(['msg' => '删除抬头成功']);
    }

}
