<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Coupon as CouponModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\User as UserModel;

class ExamPaperPayCalculator extends PayCalculator
{

    /**
     * @var ExamPaperModel
     */
    protected $paper;

    public function __construct(ExamPaperModel $paper)
    {
        $this->paper = $paper;
    }

    public function getTotalAmount()
    {
        return $this->paper->market_price;
    }

    public function handleCouponPay(CouponModel $coupon, UserModel $user)
    {
        $salePrice = $user->vip == 1 ? $this->paper->vip_price : $this->paper->market_price;

        $deductAmount = $this->getCouponDeductAmount($coupon, $salePrice);

        $this->totalAmount = $this->paper->market_price;
        $this->payAmount = $salePrice - $deductAmount;
        $this->discountAmount = $this->totalAmount - $this->payAmount;;
    }

    public function handleNormalPay(UserModel $user)
    {
        $this->totalAmount = $this->paper->market_price;
        $this->payAmount = $this->paper->market_price;

        if ($user->vip == 1) {
            $this->payAmount = $this->paper->vip_price;
        }

        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

}
