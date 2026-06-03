<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Coupon;

use App\Models\Coupon as CouponModel;
use App\Models\CouponUser as CouponUserModel;
use App\Repos\Coupon as CouponRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\Coupon as CouponValidator;
use App\Validators\CouponUser as CouponUserValidator;

class CouponRedeem extends LogicService
{

    public function handle()
    {
        $code = $this->request->getPost('code', ['trim', 'string']);

        $couponValidator = new CouponValidator();

        $coupon = $couponValidator->checkByCode($code);

        $couponValidator->checkIfActiveCoupon($coupon);

        $user = $this->getLoginUser();

        $couponUserValidator = new CouponUserValidator();

        $couponUserValidator->checkIfAllowClaim($coupon, $user);

        $couponUser = new CouponUserModel();

        $couponUser->coupon_id = $coupon->id;
        $couponUser->user_id = $user->id;
        $couponUser->channel = CouponUserModel::CHANNEL_COLLECT;
        $couponUser->create();

        $this->recountClaimedUsers($coupon);

        return $couponUser;
    }

    protected function recountClaimedUsers(CouponModel $coupon)
    {
        $couponRepo = new CouponRepo();

        $applyCount = $couponRepo->countClaimedUsers($coupon->id);

        $coupon->claim_count = $applyCount;

        $coupon->update();
    }

}
