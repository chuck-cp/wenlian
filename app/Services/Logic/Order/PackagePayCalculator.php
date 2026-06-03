<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Coupon as CouponModel;
use App\Models\Package as PackageModel;
use App\Models\User as UserModel;
use App\Repos\Package as PackageRepo;

class PackagePayCalculator extends PayCalculator
{

    /**
     * @var PackageModel
     */
    protected $package;

    public function __construct(PackageModel $package)
    {
        $this->package = $package;
    }

    public function getTotalAmount()
    {
        $packageRepo = new PackageRepo();

        $courses = $packageRepo->findCourses($this->package->id);

        $totalAmount = 0.00;

        foreach ($courses as $course) {
            $totalAmount += $course->market_price;
        }

        return $totalAmount;
    }

    public function handleCouponPay(CouponModel $coupon, UserModel $user)
    {
        $salePrice = $user->vip == 1 ? $this->package->vip_price : $this->package->market_price;

        $deductAmount = $this->getCouponDeductAmount($coupon, $salePrice);

        $this->totalAmount = $this->getTotalAmount();
        $this->payAmount = $salePrice - $deductAmount;
        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

    public function handleNormalPay(UserModel $user)
    {
        $this->totalAmount = $this->getTotalAmount();
        $this->payAmount = $this->package->market_price;

        if ($user->vip == 1) {
            $this->payAmount = $this->package->vip_price;
        }

        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

}
