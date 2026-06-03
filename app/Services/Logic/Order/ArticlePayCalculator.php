<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Coupon as CouponModel;
use App\Models\Article as ArticleModel;
use App\Models\User as UserModel;

class ArticlePayCalculator extends PayCalculator
{

    /**
     * @var ArticleModel
     */
    protected $article;

    public function __construct(ArticleModel $article)
    {
        $this->article = $article;
    }

    public function getTotalAmount()
    {
        return $this->article->market_price;
    }

    public function handleCouponPay(CouponModel $coupon, UserModel $user)
    {
        $salePrice = $user->vip == 1 ? $this->article->vip_price : $this->article->market_price;

        $deductAmount = $this->getCouponDeductAmount($coupon, $salePrice);

        $this->totalAmount = $this->article->market_price;
        $this->payAmount = $salePrice - $deductAmount;
        $this->discountAmount = $this->totalAmount - $this->payAmount;;
    }

    public function handleNormalPay(UserModel $user)
    {
        $this->totalAmount = $this->article->market_price;
        $this->payAmount = $this->article->market_price;

        if ($user->vip == 1) {
            $this->payAmount = $this->article->vip_price;
        }

        $this->discountAmount = $this->totalAmount - $this->payAmount;
    }

}
