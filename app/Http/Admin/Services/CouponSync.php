<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
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
use App\Repos\Coupon as CouponRepo;

class CouponSync extends Service
{

    public function syncCourseInfo(CourseModel $course)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findItemCoupon($course->id, KgSaleModel::ITEM_COURSE);

        if ($coupon && $coupon->end_time > time()) {
            $coupon->item_info = $this->getOriginCourseInfo($course);
            $coupon->update();
        }
    }

    public function syncPackageInfo(PackageModel $package)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findItemCoupon($package->id, KgSaleModel::ITEM_PACKAGE);

        if ($coupon && $coupon->end_time > time()) {
            $coupon->item_info = $this->getOriginPackageInfo($package);
            $coupon->update();
        }
    }

    public function syncVipInfo(VipModel $vip)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findItemCoupon($vip->id, KgSaleModel::ITEM_VIP);

        if ($coupon && $coupon->end_time > time()) {
            $coupon->item_info = $this->getOriginVipInfo($vip);
            $coupon->update();
        }
    }

    public function syncExamPaperInfo(ExamPaperModel $paper)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findItemCoupon($paper->id, KgSaleModel::ITEM_EXAM_PAPER);

        if ($coupon && $coupon->end_time > time()) {
            $coupon->item_info = $this->getOriginExamPaperInfo($paper);
            $coupon->update();
        }
    }

    public function syncArticleInfo(ArticleModel $article)
    {
        $couponRepo = new CouponRepo();

        $coupon = $couponRepo->findItemCoupon($article->id, KgSaleModel::ITEM_ARTICLE);

        if ($coupon && $coupon->end_time > time()) {
            $coupon->item_info = $this->getOriginArticleInfo($article);
            $coupon->update();
        }
    }

    public function getOriginCourseInfo(CourseModel $course)
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'cover' => CourseModel::getCoverPath($course->cover),
            'price' => (float)$course->market_price,
        ];
    }

    public function getOriginPackageInfo(PackageModel $package)
    {
        return [
            'id' => $package->id,
            'title' => $package->title,
            'cover' => PackageModel::getCoverPath($package->cover),
            'price' => (float)$package->market_price,
        ];
    }

    public function getOriginVipInfo(VipModel $vip)
    {
        return [
            'id' => $vip->id,
            'title' => sprintf('会员服务（%d个月）', $vip->expiry),
            'cover' => VipModel::getCoverPath($vip->cover),
            'price' => (float)$vip->price,
        ];
    }

    public function getOriginExamPaperInfo(ExamPaperModel $paper)
    {
        return [
            'id' => $paper->id,
            'title' => $paper->title,
            'cover' => ExamPaperModel::getCoverPath($paper->cover),
            'price' => (float)$paper->market_price,
        ];
    }

    public function getOriginArticleInfo(ArticleModel $article)
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'cover' => ArticleModel::getCoverPath($article->cover),
            'price' => (float)$article->market_price,
        ];
    }

}
