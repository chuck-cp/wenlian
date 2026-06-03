<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Withdraw\WithdrawCancel as WithdrawCancelService;
use App\Services\Logic\Withdraw\WithdrawCreate as WithdrawCreateService;

/**
 * @RoutePrefix("/withdraw")
 */
class WithdrawController extends Controller
{

    /**
     * @Post("/create", name="home.withdraw.create")
     */
    public function createAction()
    {
        $service = new WithdrawCreateService();

        $service->handle();

        $location = $this->url->get(
            ['for' => 'home.uc.withdraw'],
            ['action' => 'list']
        );

        $content = [
            'location' => $location,
            'msg' => '申请提现成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/cancel", name="home.withdraw.cancel")
     */
    public function cancelAction($id)
    {
        $service = new WithdrawCancelService();

        $service->handle($id);

        $location = $this->url->get(
            ['for' => 'home.uc.withdraw'],
            ['action' => 'list']
        );

        $content = [
            'location' => $location,
            'msg' => '取消提现成功',
        ];

        return $this->jsonSuccess($content);
    }

}
