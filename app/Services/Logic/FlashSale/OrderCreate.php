<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\FlashSale;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\FlashSale as FlashSaleModel;
use App\Models\KgSale as KgSaleModel;
use App\Models\Order as OrderModel;
use App\Services\Logic\Order\OrderCreate as OrderCreateService;
use App\Validators\FlashSale as FlashSaleValidator;
use App\Validators\Order as OrderValidator;

class OrderCreate extends OrderCreateService
{

    public function run($id)
    {
        $saleValidator = new FlashSaleValidator();

        $sale = $saleValidator->checkFlashSale($id);

        $user = $this->getLoginUser();

        $this->checkUserDailyOrderLimit($user);

        $saleValidator->checkIfExpired($sale->end_time);
        $saleValidator->checkIfOutSchedules($sale->schedules);
        $saleValidator->checkIfNotPaid($user->id, $sale->id);

        $queue = new Queue();

        if ($queue->pop($id) === false) {
            throw new BadRequestException('flash_sale.out_stock');
        }

        $this->amount = $sale->price;
        $this->promotion_id = $sale->id;
        $this->promotion_type = OrderModel::PROMOTION_FLASH_SALE;
        $this->promotion_info = [
            'flash_sale' => [
                'id' => $sale->id,
                'price' => $sale->price,
            ]
        ];

        $orderValidator = new OrderValidator();

        $orderValidator->checkAmount($this->amount);

        try {

            $order = new OrderModel();

            if ($sale->item_type == KgSaleModel::ITEM_COURSE) {

                $course = $orderValidator->checkCourse($sale->item_id);

                $orderValidator->checkIfBoughtCourse($user->id, $course->id);

                $order = $this->createCourseOrder($course, $user);

            } elseif ($sale->item_type == KgSaleModel::ITEM_PACKAGE) {

                $package = $orderValidator->checkPackage($sale->item_id);

                $orderValidator->checkIfBoughtPackage($user->id, $package->id);

                $order = $this->createPackageOrder($package, $user);

            } elseif ($sale->item_type == KgSaleModel::ITEM_VIP) {

                $vip = $orderValidator->checkVip($sale->item_id);

                $order = $this->createVipOrder($vip, $user);

            } elseif ($sale->item_type == KgSaleModel::ITEM_EXAM_PAPER) {

                $paper = $orderValidator->checkExamPaper($sale->item_id);

                $orderValidator->checkIfBoughtExamPaper($user->id, $paper->id);

                $order = $this->createExamPaperOrder($paper, $user);

            } elseif ($sale->item_type == KgSaleModel::ITEM_ARTICLE) {

                $article = $orderValidator->checkArticle($sale->item_id);

                $orderValidator->checkIfBoughtArticle($user->id, $article->id);

                $order = $this->createArticleOrder($article, $user);
            }

            $this->incrUserDailyOrderCount($user);

            $this->decrFlashSaleStock($sale);

            $this->saveUserOrderCache($user->id, $sale->id);

            return $order;

        } catch (\Exception $e) {

            $queue->push($sale->id);

            $this->deleteUserOrderCache($user->id, $sale->id);

            throw new BadRequestException($e->getMessage());
        }
    }

    protected function decrFlashSaleStock(FlashSaleModel $sale)
    {
        if ($sale->stock < 1) return;

        if ($sale->stock == 1) $sale->published = 0;

        $sale->stock -= 1;

        $sale->update();
    }

    protected function saveUserOrderCache($userId, $saleId)
    {
        $cache = new UserOrderCache();

        return $cache->save($userId, $saleId);
    }

    protected function deleteUserOrderCache($userId, $saleId)
    {
        $cache = new UserOrderCache();

        return $cache->delete($userId, $saleId);
    }

}
