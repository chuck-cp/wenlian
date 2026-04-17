<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Withdraw\AccountCreate as WithdrawAccountCreateService;
use App\Services\Logic\Withdraw\AccountDelete as WithdrawAccountDeleteService;
use App\Services\Logic\Withdraw\AccountVerify as WithdrawAccountVerifyService;

/**
 * @RoutePrefix("/withdraw/account")
 */
class WithdrawAccountController extends Controller
{

    /**
     * @Post("/create", name="home.withdraw_account.create")
     */
    public function createAction()
    {
        $service = new WithdrawAccountCreateService();

        $result = $service->handle();

        $order = $result['order'];

        $location = $this->url->get(
            ['for' => 'home.order.pay'],
            ['sn' => $order->sn],
        );

        $content = [
            'location' => $location,
            'msg' => '创建验证订单成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/verify", name="home.withdraw_account.verify")
     */
    public function verifyAction($id)
    {
        $service = new WithdrawAccountVerifyService();

        $account = $service->handle($id);

        $content = ['verified' => $account->verified];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="home.withdraw_account.delete")
     */
    public function deleteAction($id)
    {
        $service = new WithdrawAccountDeleteService();

        $service->handle($id);

        return $this->jsonSuccess(['msg' => '删除账户成功']);
    }

}
