<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\KgProduct as KgProductModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\Vip as VipRepo;

class ItemSalePrice
{

    public function handle($itemId, $itemType)
    {
        $result = [
            'market_price' => 0,
            'vip_price' => 0,
        ];

        if ($itemType == KgProductModel::ITEM_COURSE) {

            $courseRepo = new CourseRepo();

            $course = $courseRepo->findById($itemId);

            $result['market_price'] = $course->market_price;
            $result['vip_price'] = $course->vip_price;

        } elseif ($itemType == KgProductModel::ITEM_PACKAGE) {

            $packageRepo = new PackageRepo();

            $package = $packageRepo->findById($itemId);

            $result['market_price'] = $package->market_price;
            $result['vip_price'] = $package->vip_price;

        } elseif ($itemType == KgProductModel::ITEM_VIP) {

            $vipRepo = new VipRepo();

            $vip = $vipRepo->findById($itemId);

            $result['market_price'] = $vip->price;
            $result['vip_price'] = $vip->price;

        } elseif ($itemType == KgProductModel::ITEM_EXAM_PAPER) {

            $paperRepo = new ExamPaperRepo();

            $paper = $paperRepo->findById($itemId);

            $result['market_price'] = $paper->market_price;
            $result['vip_price'] = $paper->vip_price;

        } elseif ($itemType == KgProductModel::ITEM_ARTICLE) {

            $articleRepo = new ArticleRepo();

            $article = $articleRepo->findById($itemId);

            $result['market_price'] = $article->market_price;
            $result['vip_price'] = $article->vip_price;
        }

        return $result;
    }

}
