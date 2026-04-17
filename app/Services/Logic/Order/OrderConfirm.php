<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Order;

use App\Models\Article as ArticleModel;
use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgSale as KgSaleModel;
use App\Models\Package as PackageModel;
use App\Models\Vip as VipModel;
use App\Repos\Package as PackageRepo;
use App\Services\Logic\Coupon\CouponOrderTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\Order as OrderValidator;

class OrderConfirm extends LogicService
{

    use CouponOrderTrait;

    public function handle($itemId, $itemType)
    {
        $user = $this->getLoginUser();

        $validator = new OrderValidator();

        $validator->checkItemType($itemType);

        $allowApplyCoupon = $this->allowApplyCoupon($itemType) ? 1 : 0;

        $result = [];

        $result['total_amount'] = 0.00;
        $result['discount_amount'] = 0.00;
        $result['pay_amount'] = 0.00;

        $result['item_id'] = $itemId;
        $result['item_type'] = $itemType;
        $result['item_info'] = [];
        $result['allow_apply_coupon'] = $allowApplyCoupon;

        $calculator = null;

        if ($itemType == KgSaleModel::ITEM_COURSE) {

            $course = $validator->checkCourse($itemId);

            $result['item_info']['course'] = $this->handleCourseInfo($course);

            $calculator = new CoursePayCalculator($course);

            $calculator->handleNormalPay($user);

        } elseif ($itemType == KgSaleModel::ITEM_PACKAGE) {

            $package = $validator->checkPackage($itemId);

            $result['item_info']['package'] = $this->handlePackageInfo($package);

            $calculator = new PackagePayCalculator($package);

            $calculator->handleNormalPay($user);

        } elseif ($itemType == KgSaleModel::ITEM_VIP) {

            $vip = $validator->checkVip($itemId);

            $result['item_info']['vip'] = $this->handleVipInfo($vip);

            $calculator = new VipPayCalculator($vip);

            $calculator->handleNormalPay($user);

        } elseif ($itemType == KgSaleModel::ITEM_EXAM_PAPER) {

            $examPaper = $validator->checkExamPaper($itemId);

            $result['item_info']['exam_paper'] = $this->handleExamPaperInfo($examPaper);

            $calculator = new ExamPaperPayCalculator($examPaper);

            $calculator->handleNormalPay($user);

        } elseif ($itemType == KgSaleModel::ITEM_ARTICLE) {

            $article = $validator->checkArticle($itemId);

            $result['item_info']['article'] = $this->handleArticleInfo($article);

            $calculator = new ArticlePayCalculator($article);

            $calculator->handleNormalPay($user);
        }

        if ($calculator) {
            $result['total_amount'] = $calculator->getTotalAmount();
            $result['pay_amount'] = $calculator->getPayAmount();
            $result['discount_amount'] = $calculator->getDiscountAmount();
        }

        return $result;
    }

    protected function handleCourseInfo(CourseModel $course)
    {
        return $this->formatCourseInfo($course);
    }

    protected function handlePackageInfo(PackageModel $package)
    {
        $result = [
            'id' => $package->id,
            'title' => $package->title,
            'cover' => $package->cover,
            'market_price' => $package->market_price,
            'vip_price' => $package->vip_price,
        ];

        $packageRepo = new PackageRepo();

        $courses = $packageRepo->findCourses($package->id);

        foreach ($courses as $course) {
            $result['courses'][] = $this->formatCourseInfo($course);
        }

        return $result;
    }

    protected function handleVipInfo(VipModel $vip)
    {
        return [
            'id' => $vip->id,
            'title' => $vip->title,
            'cover' => $vip->cover,
            'expiry' => $vip->expiry,
            'price' => $vip->price,
        ];
    }

    protected function handleExamPaperInfo(ExamPaperModel $paper)
    {
        return [
            'id' => $paper->id,
            'title' => $paper->title,
            'cover' => $paper->cover,
            'duration' => $paper->duration,
            'join_count' => $paper->getJoinCount(),
            'question_count' => $paper->question_count,
            'study_expiry' => $paper->study_expiry,
            'refund_expiry' => $paper->refund_expiry,
            'market_price' => $paper->market_price,
            'vip_price' => $paper->vip_price,
        ];
    }

    protected function handleArticleInfo(ArticleModel $article)
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'cover' => $article->cover,
            'user_count' => $article->getUserCount(),
            'word_count' => $article->word_count,
            'study_expiry' => $article->study_expiry,
            'market_price' => $article->market_price,
            'vip_price' => $article->vip_price,
        ];
    }

    protected function formatCourseInfo(CourseModel $course)
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'cover' => $course->cover,
            'model' => $course->model,
            'level' => $course->level,
            'attrs' => $course->attrs,
            'user_count' => $course->getUserCount(),
            'lesson_count' => $course->lesson_count,
            'study_expiry' => $course->study_expiry,
            'refund_expiry' => $course->refund_expiry,
            'market_price' => $course->market_price,
            'vip_price' => $course->vip_price,
        ];
    }

}
