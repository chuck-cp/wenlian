<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Coupon as CouponModel;
use App\Models\User as UserModel;
use App\Models\Vip as VipModel;

class VipPayCalculator extends PayCalculator
{

    /**
     * @var VipModel
     */
    protected $vip;

    public function __construct(VipModel $vip)
    {
        $this->vip = $vip;
    }

    public function getTotalAmount()
    {
        return $this->vip->price;
    }

    public function handleCouponPay(CouponModel $coupon, UserModel $user)
    {
        $deductAmount = $this->getCouponDeductAmount($coupon, $this->vip->price);

        $this->totalAmount = $this->vip->price;
        $this->payAmount = $this->totalAmount - $deductAmount;
        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

    public function handleNormalPay(UserModel $user)
    {
        $this->totalAmount = $this->vip->price;
        $this->payAmount = $this->vip->price;
        $this->discountAmount = 0.00;
    }

}
