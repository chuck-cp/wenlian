<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\FlashSale;

use App\Models\FlashSale as FlashSaleModel;
use App\Models\KgProduct as KgProductModel;
use App\Models\User as UserModel;
use App\Services\Logic\ArticleTrait;
use App\Services\Logic\CourseTrait;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\FlashSaleTrait;
use App\Services\Logic\PackageTrait;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\VipTrait;

class SaleInfo extends LogicService
{

    use VipTrait;
    use CourseTrait;
    use PackageTrait;
    use ArticleTrait;
    use ExamPaperTrait;
    use SaleInfoTrait;
    use FlashSaleTrait;

    public function handle($id)
    {
        $this->cosUrl = kg_cos_url();

        $sale = $this->checkFlashSale($id);

        $user = $this->getCurrentUser();

        $item = $this->handleItemInfo($sale->item_type, $sale->item_info);

        $item['details'] = $this->getDetails($sale);

        $me = $this->handleMeInfo($sale, $user);

        return [
            'id' => $sale->id,
            'price' => $sale->price,
            'published' => $sale->published,
            'deleted' => $sale->deleted,
            'create_time' => $sale->create_time,
            'update_time' => $sale->update_time,
            'item' => $item,
            'me' => $me,
        ];
    }

    protected function getDetails(FlashSaleModel $sale)
    {
        $details = '';

        if ($sale->item_type == KgProductModel::ITEM_COURSE) {

            $course = $this->checkCourse($sale->item_id);

            $details = $course->details;

        } elseif ($sale->item_type == KgProductModel::ITEM_PACKAGE) {

            $package = $this->checkPackage($sale->item_id);

            $details = $package->summary;

        } elseif ($sale->item_type == KgProductModel::ITEM_VIP) {

            $vip = $this->checkVip($sale->item_id);

            $details = $vip->title;

        } elseif ($sale->item_type == KgProductModel::ITEM_EXAM_PAPER) {

            $paper = $this->checkExamPaper($sale->item_id);

            $details = $paper->title;

        } elseif ($sale->item_type == KgProductModel::ITEM_ARTICLE) {

            $article = $this->checkArticle($sale->item_id);

            $details = $article->title;
        }

        return $details;
    }

    protected function handleMeInfo(FlashSaleModel $sale, UserModel $user)
    {
        $me = ['allow_order' => 0];

        if ($user->id > 0) {
            $me['allow_order'] = 1;
        }

        return $me;
    }

}
