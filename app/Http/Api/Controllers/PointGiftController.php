<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Point\GiftInfo as GiftInfoService;
use App\Services\Logic\Point\GiftList as GiftListService;
use App\Services\Logic\Point\GiftRedeem as GiftRedeemService;

/**
 * @RoutePrefix("/api/point/gift")
 */
class PointGiftController extends Controller
{

    /**
     * @Get("/list", name="api.point_gift.list")
     */
    public function listAction()
    {
        $service = new GiftListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/{id:[0-9]+}/info", name="api.point_gift.info")
     */
    public function infoAction($id)
    {
        $service = new GiftInfoService();

        $gift = $service->handle($id);

        if ($gift['deleted'] == 1) {
            $this->notFound();
        }

        if ($gift['published'] == 0) {
            $this->notFound();
        }

        return $this->jsonSuccess(['gift' => $gift]);
    }

    /**
     * @Post("/{id:[0-9]+}/redeem", name="api.point_gift.redeem")
     */
    public function redeemAction($id)
    {
        $service = new GiftRedeemService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

}
