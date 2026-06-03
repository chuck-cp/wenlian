<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use App\Validators\Coupon as CouponValidator;

trait CouponTrait
{

    public function checkCouponById($id)
    {
        $validator = new CouponValidator();

        return $validator->checkById($id);
    }

    public function checkCouponByCode($code)
    {
        $validator = new CouponValidator();

        return $validator->checkByCode($code);
    }

}
