<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\Article as ArticleModel;
use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgSale as KgSaleModel;
use App\Models\Package as PackageModel;
use App\Models\Vip as VipModel;
use App\Repos\FlashSale as FlashSaleRepo;

class FlashSaleSync extends Service
{

    public function syncCourseInfo(CourseModel $course)
    {
        $saleRepo = new FlashSaleRepo();

        $sale = $saleRepo->findItemFlashSale($course->id, KgSaleModel::ITEM_COURSE);

        if ($sale && $sale->end_time > time()) {
            $sale->item_info = $this->getOriginCourseInfo($course);
            $sale->update();
        }
    }

    public function syncPackageInfo(PackageModel $package)
    {
        $saleRepo = new FlashSaleRepo();

        $sale = $saleRepo->findItemFlashSale($package->id, KgSaleModel::ITEM_PACKAGE);

        if ($sale && $sale->end_time > time()) {
            $sale->item_info = $this->getOriginPackageInfo($package);
            $sale->update();
        }
    }

    public function syncVipInfo(VipModel $vip)
    {
        $saleRepo = new FlashSaleRepo();

        $sale = $saleRepo->findItemFlashSale($vip->id, KgSaleModel::ITEM_VIP);

        if ($sale && $sale->end_time > time()) {
            $sale->item_info = $this->getOriginVipInfo($vip);
            $sale->update();
        }
    }

    public function syncExamPaperInfo(ExamPaperModel $paper)
    {
        $saleRepo = new FlashSaleRepo();

        $sale = $saleRepo->findItemFlashSale($paper->id, KgSaleModel::ITEM_EXAM_PAPER);

        if ($sale && $sale->end_time > time()) {
            $sale->item_info = $this->getOriginExamPaperInfo($paper);
            $sale->update();
        }
    }

    public function syncArticleInfo(ArticleModel $article)
    {
        $saleRepo = new FlashSaleRepo();

        $sale = $saleRepo->findItemFlashSale($article->id, KgSaleModel::ITEM_ARTICLE);

        if ($sale && $sale->end_time > time()) {
            $sale->item_info = $this->getOriginArticleInfo($article);
            $sale->update();
        }
    }

    public function getOriginCourseInfo(CourseModel $course)
    {
        return [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'cover' => CourseModel::getCoverPath($course->cover),
                'price' => (float)$course->market_price,
            ]
        ];
    }

    public function getOriginPackageInfo(PackageModel $package)
    {
        return [
            'package' => [
                'id' => $package->id,
                'title' => $package->title,
                'cover' => PackageModel::getCoverPath($package->cover),
                'price' => (float)$package->market_price,
            ]
        ];
    }

    public function getOriginVipInfo(VipModel $vip)
    {
        return [
            'vip' => [
                'id' => $vip->id,
                'title' => sprintf('会员服务（%d个月）', $vip->expiry),
                'cover' => VipModel::getCoverPath($vip->cover),
                'price' => (float)$vip->price,
            ]
        ];
    }

    public function getOriginExamPaperInfo(ExamPaperModel $paper)
    {
        return [
            'exam_paper' => [
                'id' => $paper->id,
                'title' => $paper->title,
                'cover' => ExamPaperModel::getCoverPath($paper->cover),
                'price' => (float)$paper->market_price,
            ]
        ];
    }

    public function getOriginArticleInfo(ArticleModel $article)
    {
        return [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'cover' => ArticleModel::getCoverPath($article->cover),
                'price' => (float)$article->market_price,
            ]
        ];
    }

}
