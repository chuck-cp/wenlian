<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Coupon\CouponClaim as CouponClaimService;
use App\Services\Logic\Coupon\CouponList as CouponListService;
use App\Services\Logic\Coupon\CouponRedeem as CouponRedeemService;

/**
 * @RoutePrefix("/api/coupon")
 */
class CouponController extends Controller
{

    /**
     * @Get("/list", name="api.coupon.list")
     */
    public function listAction()
    {
        $service = new CouponListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Post("/{id:[0-9]+}/claim", name="api.coupon.claim")
     */
    public function claimAction($id)
    {
        $service = new CouponClaimService();

        $service->handle($id);

        return $this->jsonSuccess();
    }

    /**
     * @Post("/redeem", name="api.coupon.redeem")
     */
    public function redeemAction()
    {
        $service = new CouponRedeemService();

        $service->handle();

        return $this->jsonSuccess();
    }

}
