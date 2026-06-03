<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Repos\Coupon as CouponRepo;
use App\Repos\CouponUser as CouponUserRepo;
use App\Services\Logic\Coupon\CouponOrderTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\Coupon as CouponValidator;

class CouponList extends LogicService
{

    use CouponOrderTrait;

    public function handle($itemId, $itemType)
    {
        $result = [];

        if (!$this->allowApplyCoupon($itemType)) {
            return $result;
        }

        $user = $this->getLoginUser();

        $myCouponIds = $this->getMyCouponIds($user->id);

        $couponRepo = new CouponRepo();

        $coupons = $couponRepo->findByIds($myCouponIds);

        if ($coupons->count() == 0) {
            return $result;
        }

        $salePrice = $this->getItemSalePrice($itemId, $itemType);

        $itemPrice = $user->vip == 1 ? $salePrice['vip_price'] : $salePrice['market_price'];

        $validator = new CouponValidator();

        foreach ($coupons as $coupon) {
            if ($validator->isMatchedCoupon($coupon, $itemId, $itemType, $itemPrice)) {
                $codeEncrypt = $this->crypt->encryptBase64($coupon->code);
                $result[] = [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'attrs' => $coupon->attrs,
                    'code_encrypt' => $codeEncrypt,
                    'consume_limit' => $coupon->consume_limit,
                    'total_usage' => $coupon->total_usage,
                    'user_usage' => $coupon->user_usage,
                    'item_id' => $coupon->item_id,
                    'item_type' => $coupon->item_type,
                    'item_info' => $coupon->item_info,
                    'start_time' => $coupon->start_time,
                    'end_time' => $coupon->end_time,
                ];
            }
        }

        return $result;
    }

    protected function getMyCouponIds($userId)
    {
        $repo = new CouponUserRepo();

        $where = [
            'user_id' => $userId,
            'deleted' => 0,
        ];

        $pager = $repo->paginate($where, 'latest', 1, 1000);

        $result = [];

        if ($pager->total_items > 0) {
            foreach ($pager->items as $item) {
                $result[] = $item->coupon_id;
            }
        }

        return $result;
    }

    protected function getItemSalePrice($itemId, $itemType)
    {
        $service = new ItemSalePrice();

        return $service->handle($itemId, $itemType);
    }

}
