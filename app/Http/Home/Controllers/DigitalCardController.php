<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\DigitalCard\CardRedeem as CardRedeemService;

/**
 * @RoutePrefix("/digital/card")
 */
class DigitalCardController extends Controller
{

    /**
     * @Post("/redeem", name="home.digital_card.redeem")
     */
    public function redeemAction()
    {
        $service = new CardRedeemService();

        $service->handle();

        $location = $this->url->get(['for'=>'home.uc.digital_redeems']);

        $content = [
            'location' => $location,
            'msg' => '兑换成功',
        ];

        return $this->jsonSuccess($content);
    }

}
