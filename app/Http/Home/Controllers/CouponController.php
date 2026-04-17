<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Coupon\CouponClaim as CouponClaimService;
use App\Services\Logic\Coupon\CouponList as CouponListService;
use App\Services\Logic\Coupon\CouponRedeem as CouponRedeemService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/coupon")
 */
class CouponController extends Controller
{

    /**
     * @Get("/list", name="home.coupon.list")
     */
    public function listAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getCouponListUrl();
            return $this->response->redirect($location);
        }

        $this->seo->prependTitle('领券中心');
    }

    /**
     * @Get("/pager", name="home.coupon.pager")
     */
    public function pagerAction()
    {
        $service = new CouponListService();

        $pager = $service->handle();

        $pager->target = 'coupon-list';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('coupon/pager');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Post("/{id:[0-9]+}/claim", name="home.coupon.claim")
     */
    public function claimAction($id)
    {
        $service = new CouponClaimService();

        $service->handle($id);

        return $this->jsonSuccess(['msg' => '领取优惠券成功']);
    }

    /**
     * @Post("/redeem", name="home.coupon.redeem")
     */
    public function redeemAction()
    {
        $service = new CouponRedeemService();

        $service->handle();

        $location = $this->url->get(['for' => 'home.uc.coupons']);

        $content = [
            'location' => $location,
            'msg' => '兑换优惠券成功',
        ];

        return $this->jsonSuccess($content);
    }

}
