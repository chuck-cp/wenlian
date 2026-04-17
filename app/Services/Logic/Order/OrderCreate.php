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
use App\Models\Order as OrderModel;
use App\Models\Package as PackageModel;
use App\Models\User as UserModel;
use App\Models\Vip as VipModel;
use App\Repos\Package as PackageRepo;
use App\Services\Logic\Coupon\CouponOrderTrait;
use App\Services\Logic\Service as LogicService;
use App\Traits\Client as ClientTrait;
use App\Validators\Coupon as CouponValidator;
use App\Validators\CouponUser as CouponUserValidator;
use App\Validators\Order as OrderValidator;
use App\Validators\UserLimit as UserLimitValidator;

class OrderCreate extends LogicService
{

    /**
     * @var float 订单金额
     */
    protected $amount = 0.00;

    /**
     * @var int 促销编号
     */
    protected $promotion_id = 0;

    /**
     * @var int 促销类型
     */
    protected $promotion_type = 0;

    /**
     * @var array 促销信息
     */
    protected $promotion_info = [];

    use ClientTrait;
    use CouponOrderTrait;

    public function handle()
    {
        $itemId = $this->request->getPost('item_id', ['trim', 'int']);
        $itemType = $this->request->getPost('item_type', ['trim', 'int']);
        $couponCode = $this->request->getPost('coupon_code', ['trim', 'string']);

        $user = $this->getLoginUser();

        $this->checkUserDailyOrderLimit($user);

        $coupon = null;

        $couponUser = null;

        /**
         * 检查优惠码
         */
        if (!empty($couponCode)) {

            $couponValidator = new CouponValidator();

            $coupon = $couponValidator->checkByEncode($couponCode);

            $couponUserValidator = new CouponUserValidator();

            $couponUser = $couponUserValidator->checkCouponUser($coupon->id, $user->id);

            $this->promotion_id = $coupon->id;
            $this->promotion_type = OrderModel::PROMOTION_COUPON;
            $this->promotion_info = [
                'coupon' => [
                    'id' => $coupon->id,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                ]
            ];
        }

        $orderValidator = new OrderValidator();

        $orderValidator->checkItemType($itemType);

        $order = null;

        if ($itemType == KgSaleModel::ITEM_COURSE) {

            $course = $orderValidator->checkCourse($itemId);

            $orderValidator->checkIfBoughtCourse($user->id, $course->id);

            $calculator = new CoursePayCalculator($course);

            if ($coupon) {
                $calculator->handleCouponPay($coupon, $user);
            } else {
                $calculator->handleNormalPay($user);
            }

            $this->amount = $calculator->getPayAmount();

            $orderValidator->checkAmount($this->amount);

            $order = $this->createCourseOrder($course, $user);

        } elseif ($itemType == KgSaleModel::ITEM_PACKAGE) {

            $package = $orderValidator->checkPackage($itemId);

            $orderValidator->checkIfBoughtPackage($user->id, $package->id);

            $calculator = new PackagePayCalculator($package);

            if ($coupon) {
                $calculator->handleCouponPay($coupon, $user);
            } else {
                $calculator->handleNormalPay($user);
            }

            $this->amount = $calculator->getPayAmount();

            $orderValidator->checkAmount($this->amount);

            $order = $this->createPackageOrder($package, $user);

        } elseif ($itemType == KgSaleModel::ITEM_VIP) {

            $vip = $orderValidator->checkVip($itemId);

            $calculator = new VipPayCalculator($vip);

            if ($coupon) {
                $calculator->handleCouponPay($coupon, $user);
            } else {
                $calculator->handleNormalPay($user);
            }

            $this->amount = $calculator->getPayAmount();

            $orderValidator->checkAmount($this->amount);

            $order = $this->createVipOrder($vip, $user);

        } elseif ($itemType == KgSaleModel::ITEM_EXAM_PAPER) {

            $paper = $orderValidator->checkExamPaper($itemId);

            $orderValidator->checkIfBoughtExamPaper($user->id, $paper->id);

            $calculator = new ExamPaperPayCalculator($paper);

            if ($coupon) {
                $calculator->handleCouponPay($coupon, $user);
            } else {
                $calculator->handleNormalPay($user);
            }

            $this->amount = $calculator->getPayAmount();

            $orderValidator->checkAmount($this->amount);

            $order = $this->createExamPaperOrder($paper, $user);

        } elseif ($itemType == KgSaleModel::ITEM_ARTICLE) {

            $article = $orderValidator->checkArticle($itemId);

            $orderValidator->checkIfBoughtArticle($user->id, $article->id);

            $calculator = new ArticlePayCalculator($article);

            if ($coupon) {
                $calculator->handleCouponPay($coupon, $user);
            } else {
                $calculator->handleNormalPay($user);
            }

            $this->amount = $calculator->getPayAmount();

            $orderValidator->checkAmount($this->amount);

            $order = $this->createArticleOrder($article, $user);
        }

        if ($couponUser) {
            $this->recountCouponUserAppliedOrders($couponUser);
        }

        if ($coupon) {
            $this->recountCouponAppliedOrders($coupon);
        }

        $this->incrUserDailyOrderCount($user);

        return $order;
    }

    protected function createCourseOrder(CourseModel $course, UserModel $user)
    {
        $itemInfo = [];

        $itemInfo['course'] = $this->handleCourseInfo($course);

        $order = new OrderModel();

        $order->owner_id = $user->id;
        $order->item_id = $course->id;
        $order->item_type = KgSaleModel::ITEM_COURSE;
        $order->item_info = $itemInfo;
        $order->client_type = $this->getClientType();
        $order->client_ip = $this->getClientIp();
        $order->subject = "课程 - {$course->title}";
        $order->amount = $this->amount;
        $order->promotion_id = $this->promotion_id;
        $order->promotion_type = $this->promotion_type;
        $order->promotion_info = $this->promotion_info;

        $order->create();

        return $order;
    }

    protected function createPackageOrder(PackageModel $package, UserModel $user)
    {
        $packageRepo = new PackageRepo();

        $courses = $packageRepo->findCourses($package->id);

        $itemInfo = [];

        $itemInfo['package'] = $this->handlePackageInfo($package);

        foreach ($courses as $course) {
            $itemInfo['courses'][] = $this->handleCourseInfo($course);
        }

        $order = new OrderModel();

        $order->owner_id = $user->id;
        $order->item_id = $package->id;
        $order->item_type = KgSaleModel::ITEM_PACKAGE;
        $order->item_info = $itemInfo;
        $order->client_type = $this->getClientType();
        $order->client_ip = $this->getClientIp();
        $order->subject = "套餐 - {$package->title}";
        $order->amount = $this->amount;
        $order->promotion_id = $this->promotion_id;
        $order->promotion_type = $this->promotion_type;
        $order->promotion_info = $this->promotion_info;

        $order->create();

        return $order;
    }

    protected function createVipOrder(VipModel $vip, UserModel $user)
    {
        $itemInfo = [];

        $itemInfo['vip'] = $this->handleVipInfo($vip, $user);

        $order = new OrderModel();

        $order->owner_id = $user->id;
        $order->item_id = $vip->id;
        $order->item_type = KgSaleModel::ITEM_VIP;
        $order->item_info = $itemInfo;
        $order->client_type = $this->getClientType();
        $order->client_ip = $this->getClientIp();
        $order->subject = "会员 - 会员服务（{$vip->title}）";
        $order->amount = $this->amount;
        $order->promotion_id = $this->promotion_id;
        $order->promotion_type = $this->promotion_type;
        $order->promotion_info = $this->promotion_info;

        $order->create();

        return $order;
    }

    protected function createExamPaperOrder(ExamPaperModel $paper, UserModel $user)
    {
        $itemInfo = [];

        $itemInfo['exam_paper'] = $this->handleExamPaperInfo($paper);

        $order = new OrderModel();

        $order->owner_id = $user->id;
        $order->item_id = $paper->id;
        $order->item_type = KgSaleModel::ITEM_EXAM_PAPER;
        $order->item_info = $itemInfo;
        $order->client_type = $this->getClientType();
        $order->client_ip = $this->getClientIp();
        $order->subject = "试卷 - {$paper->title}";
        $order->amount = $this->amount;
        $order->promotion_id = $this->promotion_id;
        $order->promotion_type = $this->promotion_type;
        $order->promotion_info = $this->promotion_info;

        $order->create();

        return $order;
    }

    protected function createArticleOrder(ArticleModel $article, UserModel $user)
    {
        $itemInfo = [];

        $itemInfo['article'] = $this->handleArticleInfo($article);

        $order = new OrderModel();

        $order->owner_id = $user->id;
        $order->item_id = $article->id;
        $order->item_type = KgSaleModel::ITEM_ARTICLE;
        $order->item_info = $itemInfo;
        $order->client_type = $this->getClientType();
        $order->client_ip = $this->getClientIp();
        $order->subject = "专栏 - {$article->title}";
        $order->amount = $this->amount;
        $order->promotion_id = $this->promotion_id;
        $order->promotion_type = $this->promotion_type;
        $order->promotion_info = $this->promotion_info;

        $order->create();

        return $order;
    }

    protected function handleCourseInfo(CourseModel $course)
    {
        $studyExpiryTime = strtotime("+{$course->study_expiry} months");
        $refundExpiryTime = strtotime("+{$course->refund_expiry} days");

        $course->cover = CourseModel::getCoverPath($course->cover);

        return [
            'id' => $course->id,
            'title' => $course->title,
            'cover' => $course->cover,
            'model' => $course->model,
            'attrs' => $course->attrs,
            'market_price' => $course->market_price,
            'vip_price' => $course->vip_price,
            'study_expiry' => $course->study_expiry,
            'refund_expiry' => $course->refund_expiry,
            'study_expiry_time' => $studyExpiryTime,
            'refund_expiry_time' => $refundExpiryTime,
        ];
    }

    protected function handlePackageInfo(PackageModel $package)
    {
        $package->cover = PackageModel::getCoverPath($package->cover);

        return [
            'id' => $package->id,
            'title' => $package->title,
            'cover' => $package->cover,
            'market_price' => $package->market_price,
            'vip_price' => $package->vip_price,
        ];
    }

    protected function handleVipInfo(VipModel $vip, UserModel $user)
    {
        $baseTime = $user->vip_expiry_time > time() ? $user->vip_expiry_time : time();
        $expiryTime = strtotime("+{$vip->expiry} months", $baseTime);

        $vip->cover = VipModel::getCoverPath($vip->cover);

        return [
            'id' => $vip->id,
            'title' => $vip->title,
            'cover' => $vip->cover,
            'price' => $vip->price,
            'expiry' => $vip->expiry,
            'expiry_time' => $expiryTime,
        ];
    }

    protected function handleExamPaperInfo(ExamPaperModel $paper)
    {
        $studyExpiryTime = strtotime("+{$paper->study_expiry} months");
        $refundExpiryTime = strtotime("+{$paper->refund_expiry} days");

        $paper->cover = ExamPaperModel::getCoverPath($paper->cover);

        return [
            'id' => $paper->id,
            'title' => $paper->title,
            'cover' => $paper->cover,
            'market_price' => $paper->market_price,
            'vip_price' => $paper->vip_price,
            'study_expiry' => $paper->study_expiry,
            'refund_expiry' => $paper->refund_expiry,
            'study_expiry_time' => $studyExpiryTime,
            'refund_expiry_time' => $refundExpiryTime,
        ];
    }

    protected function handleArticleInfo(ArticleModel $article)
    {
        $studyExpiryTime = strtotime("+{$article->study_expiry} months");

        $article->cover = ExamPaperModel::getCoverPath($article->cover);

        return [
            'id' => $article->id,
            'title' => $article->title,
            'cover' => $article->cover,
            'market_price' => $article->market_price,
            'vip_price' => $article->vip_price,
            'study_expiry' => $article->study_expiry,
            'study_expiry_time' => $studyExpiryTime,
        ];
    }

    protected function incrUserDailyOrderCount(UserModel $user)
    {
        $this->eventsManager->fire('UserDailyCounter:incrOrderCount', $this, $user);
    }

    protected function checkUserDailyOrderLimit(UserModel $user)
    {
        $validator = new UserLimitValidator();

        $validator->checkDailyOrderLimit($user);
    }

}
