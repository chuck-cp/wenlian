<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\DigitalCard\CardInfo as CardInfoService;
use App\Services\Logic\DigitalCard\CardRedeem as CardRedeemService;

/**
 * @RoutePrefix("/api/digital/card")
 */
class DigitalCardController extends Controller
{

    /**
     * @Get("/info", name="api.digital_card.info")
     */
    public function infoAction()
    {
        $code = $this->request->getQuery('code', ['trim', 'string']);

        $service = new CardInfoService();

        $digitalCard = $service->handle($code);

        return $this->jsonSuccess(['digital_card' => $digitalCard]);
    }

    /**
     * @Post("/redeem", name="api.digital_card.redeem")
     */
    public function redeemAction()
    {
        $service = new CardRedeemService();

        $digitalCard = $service->handle();

        $service = new CardInfoService();

        $digitalCard = $service->handle($digitalCard->code);

        return $this->jsonSuccess(['digital_card' => $digitalCard]);
    }

}
