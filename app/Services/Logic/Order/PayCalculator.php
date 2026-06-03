<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Coupon as CouponModel;
use App\Models\User as UserModel;

abstract class PayCalculator
{

    /**
     * @var float 商品总价
     */
    protected $totalAmount = 0.00;

    /**
     * @var float 支付金额
     */
    protected $payAmount = 0.00;

    /**
     * @var float 优惠金额
     */
    protected $discountAmount = 0.00;

    public function getPayAmount()
    {
        return $this->payAmount;
    }

    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    protected function getCouponDeductAmount(CouponModel $coupon, $salePrice)
    {
        $deductAmount = 0.00;

        if ($coupon->type == CouponModel::TYPE_FIXED_AMOUNT) {
            $deductAmount = $coupon->attrs['deduct_amount'];
        } elseif ($coupon->type == CouponModel::TYPE_PERCENTAGE) {
            $deductAmount = round($salePrice * $coupon->attrs['discount_rate'] / 100, 2);
            if ($coupon->attrs['max_deduct_amount'] > 0) {
                $deductAmount = $coupon->attrs['max_deduct_amount'];
            }
        }

        if ($deductAmount > $salePrice) {
            $deductAmount = $salePrice;
        }

        return $deductAmount;
    }

    abstract public function getTotalAmount();

    abstract public function handleNormalPay(UserModel $user);

    abstract public function handleCouponPay(CouponModel $coupon, UserModel $user);

}
