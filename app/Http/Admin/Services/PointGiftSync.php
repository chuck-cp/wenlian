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
use App\Repos\PointGift as PointGiftRepo;

class PointGiftSync extends Service
{

    public function syncCourseInfo(CourseModel $course)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findItemGift($course->id, KgSaleModel::ITEM_COURSE);

        if ($gift) {
            $gift->name = $course->title;
            $gift->cover = $course->cover;
            $gift->attrs = $this->getOriginCourseInfo($course);
            $gift->update();
        }
    }

    public function syncPackageInfo(PackageModel $package)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findItemGift($package->id, KgSaleModel::ITEM_PACKAGE);

        if ($gift) {
            $gift->name = $package->title;
            $gift->cover = $package->cover;
            $gift->attrs = $this->getOriginPackageInfo($package);
            $gift->update();
        }
    }

    public function syncVipInfo(VipModel $vip)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findItemGift($vip->id, KgSaleModel::ITEM_VIP);

        if ($gift) {
            $gift->name = sprintf('会员服务（%d个月）', $vip->expiry);
            $gift->cover = $vip->cover;
            $gift->attrs = $this->getOriginVipInfo($vip);
            $gift->update();
        }
    }

    public function syncExamPaperInfo(ExamPaperModel $paper)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findItemGift($paper->id, KgSaleModel::ITEM_EXAM_PAPER);

        if ($gift) {
            $gift->name = $paper->title;
            $gift->cover = $paper->cover;
            $gift->attrs = $this->getOriginExamPaperInfo($paper);
            $gift->update();
        }
    }

    public function syncArticleInfo(ArticleModel $article)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findItemGift($article->id, KgSaleModel::ITEM_ARTICLE);

        if ($gift) {
            $gift->name = $article->title;
            $gift->cover = $article->cover;
            $gift->attrs = $this->getOriginArticleInfo($article);
            $gift->update();
        }
    }

    public function getOriginCourseInfo(CourseModel $course)
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'cover' => CourseModel::getCoverPath($course->cover),
            'price' => $course->market_price,
        ];
    }

    public function getOriginPackageInfo(PackageModel $package)
    {
        return [
            'id' => $package->id,
            'title' => $package->title,
            'cover' => PackageModel::getCoverPath($package->cover),
            'price' => $package->market_price,
        ];
    }

    public function getOriginVipInfo(VipModel $vip)
    {
        return [
            'id' => $vip->id,
            'title' => sprintf('会员服务（%d个月）', $vip->expiry),
            'cover' => VipModel::getCoverPath($vip->cover),
            'price' => $vip->price,
        ];
    }

    public function getOriginExamPaperInfo(ExamPaperModel $paper)
    {
        return [
            'id' => $paper->id,
            'title' => $paper->title,
            'cover' => ExamPaperModel::getCoverPath($paper->cover),
            'price' => $paper->market_price,
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
