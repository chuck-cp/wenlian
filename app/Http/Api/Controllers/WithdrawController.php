<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Withdraw\WithdrawCancel as WithdrawCancelService;
use App\Services\Logic\Withdraw\WithdrawCreate as WithdrawCreateService;
use App\Services\Logic\Withdraw\WithdrawInfo as WithdrawInfoService;

/**
 * @RoutePrefix("/api/withdraw")
 */
class WithdrawController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.invoice.info")
     */
    public function infoAction($id)
    {
        $service = new WithdrawInfoService();

        $withdraw = $service->handle($id);

        return $this->jsonSuccess(['withdraw' => $withdraw]);
    }

    /**
     * @Post("/create", name="api.withdraw.create")
     */
    public function createAction()
    {
        $service = new WithdrawCreateService();

        $withdraw = $service->handle();

        $service = new WithdrawInfoService();

        $withdraw = $service->handle($withdraw->id);

        return $this->jsonSuccess(['withdraw' => $withdraw]);
    }

    /**
     * @Post("/{id:[0-9]+}/cancel", name="api.withdraw.cancel")
     */
    public function cancelAction($id)
    {
        $service = new WithdrawCancelService();

        $withdraw = $service->handle($id);

        $service = new WithdrawInfoService();

        $withdraw = $service->handle($withdraw->id);

        return $this->jsonSuccess(['withdraw' => $withdraw]);
    }

}
