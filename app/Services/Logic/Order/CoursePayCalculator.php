<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Coupon as CouponModel;
use App\Models\Course as CourseModel;
use App\Models\User as UserModel;

class CoursePayCalculator extends PayCalculator
{

    /**
     * @var CourseModel
     */
    protected $course;

    public function __construct(CourseModel $course)
    {
        $this->course = $course;
    }

    public function getTotalAmount()
    {
        return $this->course->market_price;
    }

    public function handleCouponPay(CouponModel $coupon, UserModel $user)
    {
        $salePrice = $user->vip == 1 ? $this->course->vip_price : $this->course->market_price;

        $deductAmount = $this->getCouponDeductAmount($coupon, $salePrice);

        $this->totalAmount = $this->course->market_price;
        $this->payAmount = $salePrice - $deductAmount;
        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

    public function handleNormalPay(UserModel $user)
    {
        $this->totalAmount = $this->course->market_price;
        $this->payAmount = $this->course->market_price;

        if ($user->vip == 1) {
            $this->payAmount = $this->course->vip_price;
        }

        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

}
