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
use App\Repos\Distribution as DistributionRepo;

class DistributionSync extends Service
{

    public function syncCourseInfo(CourseModel $course)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findItemDistribution($course->id, KgSaleModel::ITEM_COURSE);

        if ($dist && $dist->end_time > time()) {
            $dist->item_info = $this->getOriginCourseInfo($course);
            $dist->update();
        }
    }

    public function syncPackageInfo(PackageModel $package)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findItemDistribution($package->id, KgSaleModel::ITEM_PACKAGE);

        if ($dist && $dist->end_time > time()) {
            $dist->item_info = $this->getOriginPackageInfo($package);
            $dist->update();
        }
    }

    public function syncVipInfo(VipModel $vip)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findItemDistribution($vip->id, KgSaleModel::ITEM_VIP);

        if ($dist && $dist->end_time > time()) {
            $dist->item_info = $this->getOriginVipInfo($vip);
            $dist->update();
        }
    }

    public function syncExamPaperInfo(ExamPaperModel $paper)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findItemDistribution($paper->id, KgSaleModel::ITEM_EXAM_PAPER);

        if ($dist && $dist->end_time > time()) {
            $dist->item_info = $this->getOriginExamPaperInfo($paper);
            $dist->update();
        }
    }

    public function syncArticleInfo(ArticleModel $article)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findItemDistribution($article->id, KgSaleModel::ITEM_ARTICLE);

        if ($dist && $dist->end_time > time()) {
            $dist->item_info = $this->getOriginArticleInfo($article);
            $dist->update();
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
