<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Withdraw\AccountCreate as WithdrawAccountCreateService;
use App\Services\Logic\Withdraw\AccountDelete as WithdrawAccountDeleteService;
use App\Services\Logic\Withdraw\AccountInfo as WithdrawAccountInfoService;

/**
 * @RoutePrefix("/api/withdraw/account")
 */
class WithdrawAccountController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.withdraw_account.info")
     */
    public function infoAction($id)
    {
        $service = new WithdrawAccountInfoService();

        $account = $service->handle($id);

        return $this->jsonSuccess(['account' => $account]);
    }

    /**
     * @Post("/create", name="api.withdraw_account.create")
     */
    public function createAction()
    {
        $service = new WithdrawAccountCreateService();

        $content = $service->handle();

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="api.withdraw_account.delete")
     */
    public function deleteAction($id)
    {
        $service = new WithdrawAccountDeleteService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

}
