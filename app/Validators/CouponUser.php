<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Coupon as CouponModel;
use App\Models\User as UserModel;
use App\Repos\CouponUser as CouponUserRepo;

class CouponUser extends Validator
{

    public function checkCouponUser($couponId, $userId)
    {
        $repo = new CouponUserRepo();

        $couponUser = $repo->findCouponUser($couponId, $userId);

        if (!$couponUser || $couponUser->deleted == 1) {
            throw new BadRequestException('coupon_user.not_found');
        }

        return $couponUser;
    }

    public function checkIfAllowClaim(CouponModel $coupon, UserModel $user): void
    {
        $repo = new CouponUserRepo();

        $couponUser = $repo->findCouponUser($coupon->id, $user->id);

        if ($couponUser) {
            throw new BadRequestException('coupon_user.has_claimed');
        }
    }

    public function checkIfAllowApply(CouponModel $coupon, UserModel $user): void
    {
        $validator = new CouponUser();

        $couponUser = $validator->checkCouponUser($coupon->id, $user->id);

        if ($couponUser->apply_count >= $coupon->user_usage) {
            throw new BadRequestException('coupon_user.reach_user_usage_limit');
        }

        if ($coupon->apply_count >= $coupon->total_usage) {
            throw new BadRequestException('coupon_user.reach_total_usage_limit');
        }
    }

}
