<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\KgSale as KgSaleModel;
use App\Services\Logic\Service as LogicService;
use App\Validators\Coupon as CouponValidator;
use App\Validators\CouponUser as CouponUserValidator;
use App\Validators\Order as OrderValidator;

class CouponApply extends LogicService
{

    public function handle()
    {
        $itemId = $this->request->getPost('item_id', ['trim', 'int']);
        $itemType = $this->request->getPost('item_type', ['trim', 'int']);
        $couponCode = $this->request->getPost('coupon_code', ['trim', 'string']);

        $user = $this->getLoginUser();

        $couponValidator = new CouponValidator();

        $coupon = $couponValidator->checkByEncode($couponCode);

        $orderValidator = new OrderValidator();

        $orderValidator->checkItemType($itemType);

        $couponUserValidator = new CouponUserValidator();

        $couponUserValidator->checkIfAllowApply($coupon, $user);

        $result = [
            'total_amount' => 0.00,
            'discount_amount' => 0.00,
            'pay_amount' => 0.00,
        ];

        $calculator = null;

        if ($itemType == KgSaleModel::ITEM_COURSE) {

            $course = $orderValidator->checkCourse($itemId);

            $calculator = new CoursePayCalculator($course);

            $calculator->handleCouponPay($coupon, $user);

        } elseif ($itemType == KgSaleModel::ITEM_PACKAGE) {

            $package = $orderValidator->checkPackage($itemId);

            $calculator = new PackagePayCalculator($package);

            $calculator->handleCouponPay($coupon, $user);

        } elseif ($itemType == KgSaleModel::ITEM_VIP) {

            $vip = $orderValidator->checkVip($itemId);

            $calculator = new VipPayCalculator($vip);

            $calculator->handleCouponPay($coupon, $user);

        } elseif ($itemType == KgSaleModel::ITEM_EXAM_PAPER) {

            $paper = $orderValidator->checkExamPaper($itemId);

            $calculator = new ExamPaperPayCalculator($paper);

            $calculator->handleCouponPay($coupon, $user);

        } elseif ($itemType == KgSaleModel::ITEM_ARTICLE) {

            $article = $orderValidator->checkArticle($itemId);

            $calculator = new ArticlePayCalculator($article);

            $calculator->handleCouponPay($coupon, $user);
        }

        if ($calculator) {
            $result = [
                'total_amount' => $calculator->getTotalAmount(),
                'discount_amount' => $calculator->getDiscountAmount(),
                'pay_amount' => $calculator->getPayAmount(),
            ];
            array_walk($result, function (&$item) {
                $item = sprintf('%0.2f', $item);
            });
        }

        return $result;
    }

}
