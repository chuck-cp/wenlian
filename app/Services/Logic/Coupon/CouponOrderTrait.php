<?php
/**
 * @copyright Copyright (c) 2024 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Coupon;

use App\Models\Coupon as CouponModel;
use App\Models\CouponUser as CouponUserModel;
use App\Models\KgProduct as KgProductModel;
use App\Repos\Coupon as CouponRepo;
use App\Repos\CouponUser as CouponUserRepo;

trait CouponOrderTrait
{

    protected function revokeAppliedCoupon(int $couponId, int $userId): void
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findById($couponId);

        $this->recountCouponAppliedOrders($coupon);

        $couponUserRepo = new CouponUserRepo();

        $couponUser = $couponUserRepo->findCouponUser($couponId, $userId);

        $this->recountCouponUserAppliedOrders($couponUser);
    }

    protected function recountCouponUserAppliedOrders(CouponUserModel $couponUser): void
    {
        $couponUserRepo = new CouponUserRepo();

        $applyCount = $couponUserRepo->countAppliedOrders($couponUser->coupon_id, $couponUser->user_id);

        $couponUser->apply_count = $applyCount;

        $couponUser->update();
    }

    protected function recountCouponAppliedOrders(CouponModel $coupon): void
    {
        $couponRepo = new CouponRepo();

        $applyCount = $couponRepo->countAppliedOrders($coupon->id);

        $coupon->apply_count = $applyCount;

        $coupon->update();
    }

    protected function allowApplyCoupon(int $itemType): bool
    {
        $whitelist = [
            KgProductModel::ITEM_COURSE,
            KgProductModel::ITEM_PACKAGE,
            KgProductModel::ITEM_VIP,
            KgProductModel::ITEM_EXAM_PAPER,
            KgProductModel::ITEM_ARTICLE,
        ];

        return in_array($itemType, $whitelist);
    }

}
