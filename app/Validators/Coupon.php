<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\Coupon as CouponModel;
use App\Repos\Coupon as CouponRepo;

class Coupon extends Validator
{

    public function checkById($id)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findById($id);

        if (!$coupon) {
            throw new BadRequestException('coupon.not_found');
        }

        return $coupon;
    }

    public function checkByCode($code)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findByCode($code);

        if (!$coupon) {
            throw new BadRequestException('coupon.not_found');
        }

        return $coupon;
    }

    public function checkByEncode($code)
    {
        $decode = $this->crypt->decryptBase64($code);

        return $this->checkByCode($decode);
    }

    public function checkName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 30) {
            throw new BadRequestException('coupon.invalid_name');
        }

        return $value;
    }

    public function checkType($type)
    {
        if (!array_key_exists($type, CouponModel::types())) {
            throw new BadRequestException('coupon.invalid_type');
        }

        return $type;
    }

    public function checkItemType($itemType)
    {
        $itemType = intval($itemType);

        /**
         * 不限制商品类型
         */
        if ($itemType == 0) return $itemType;

        if (!array_key_exists($itemType, CouponModel::itemTypes())) {
            throw new BadRequestException('coupon.invalid_item_type');
        }

        return $itemType;
    }

    public function checkCourse($id)
    {
        $validator = new Course();

        return $validator->checkCourse($id);
    }

    public function checkPackage($id)
    {
        $validator = new Package();

        return $validator->checkPackage($id);
    }

    public function checkVip($id)
    {
        $validator = new Vip();

        return $validator->checkVip($id);
    }

    public function checkExamPaper($id)
    {
        $validator = new ExamPaper();

        return $validator->checkExamPaper($id);
    }

    public function checkArticle($id)
    {
        $validator = new Article();

        return $validator->checkArticle($id);
    }

    public function checkStartTime($startTime)
    {
        if (!CommonValidator::date($startTime, 'Y-m-d H:i:s')) {
            throw new BadRequestException('coupon.invalid_start_time');
        }

        return strtotime($startTime);
    }

    public function checkEndTime($endTime)
    {
        if (!CommonValidator::date($endTime, 'Y-m-d H:i:s')) {
            throw new BadRequestException('coupon.invalid_end_time');
        }

        return strtotime($endTime);
    }

    public function checkTimeRange($startTime, $endTime)
    {
        if ($startTime >= $endTime) {
            throw new BadRequestException('coupon.invalid_time_range');
        }
    }

    public function checkTotalUsage($limit)
    {
        $value = $this->filter->sanitize($limit, ['trim', 'int']);

        if ($value < 1) {
            throw new BadRequestException('coupon.invalid_total_usage');
        }

        return $value;
    }

    public function checkDeductAmount($amount)
    {
        $value = $this->filter->sanitize($amount, ['trim', 'float']);

        $value = round($value, 2);

        if ($value < 0.01) {
            throw new BadRequestException('coupon.invalid_deduct_amount');
        }

        return $value;
    }

    public function checkConsumeLimit($limit)
    {
        $value = $this->filter->sanitize($limit, ['trim', 'float']);

        $value = round($value, 2);

        if ($value < 0.00) {
            throw new BadRequestException('coupon.invalid_consume_limit');
        }

        return $value;
    }

    public function checkUserUsage($limit)
    {
        $value = $this->filter->sanitize($limit, ['trim', 'int']);

        if ($value < 1) {
            throw new BadRequestException('coupon.invalid_user_usage');
        }

        return $value;
    }

    public function checkDiscountRate($rate)
    {
        $value = $this->filter->sanitize($rate, ['trim', 'int']);

        if ($value < 1) {
            throw new BadRequestException('coupon.invalid_discount_rate');
        }

        return $value;
    }

    public function checkMaxDeductAmount($amount)
    {
        $value = $this->filter->sanitize($amount, ['trim', 'float']);

        $value = round($value, 2);

        if ($value < 1) {
            throw new BadRequestException('coupon.invalid_max_deduct_amount');
        }

        return $value;
    }

    public function checkPrivateStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('coupon.invalid_private_status');
        }

        return $status;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('coupon.invalid_publish_status');
        }

        return $status;
    }

    public function checkIfActiveCoupon(CouponModel $coupon)
    {
        $case1 = $coupon->published == 1;
        $case2 = $coupon->deleted == 0;
        $case3 = $coupon->start_time < time() && $coupon->end_time > time();
        $case4 = $coupon->total_usage > $coupon->apply_count;

        $ok = $case1 && $case2 && $case3 && $case4;

        if (!$ok) {
            throw new BadRequestException('coupon.not_active');
        }
    }

    public function isMatchedCoupon(CouponModel $coupon, $itemId, $itemType, $itemPrice)
    {
        if ($coupon->start_time > time() || $coupon->end_time < time()) {
            return false;
        }

        if ($coupon->apply_count >= $coupon->total_usage) {
            return false;
        }

        $itemTypeOk = false;
        $itemPriceOk = false;

        /**
         * 不限商品类型
         */
        if ($coupon->item_type == 0) $itemTypeOk = true;

        if ($coupon->item_type == $itemType) {
            /**
             * 不限具体商品
             */
            if ($coupon->item_id == 0) $itemTypeOk = true;

            if ($itemId == $coupon->item_id) $itemTypeOk = true;
        }

        /**
         * 不限最低消费
         */
        if ($coupon->consume_limit == 0) $itemPriceOk = true;

        if ($coupon->consume_limit <= $itemPrice) $itemPriceOk = true;

        return $itemTypeOk && $itemPriceOk;
    }

}
