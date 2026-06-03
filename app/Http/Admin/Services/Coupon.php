<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\OrderList as OrderListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Coupon as CouponModel;
use App\Models\KgSale as KgSaleModel;
use App\Models\Order as OrderModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Coupon as CouponRepo;
use App\Repos\CouponUser as CouponUserRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Order as OrderRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\Vip as VipRepo;
use App\Validators\Coupon as CouponValidator;

class Coupon extends Service
{

    public function getTypes()
    {
        return CouponModel::types();
    }

    public function getItemTypes()
    {
        return CouponModel::itemTypes();
    }

    public function getDiscountRates()
    {
        return range(1, 50);
    }

    public function getXmCourses(CouponModel $coupon)
    {
        $courseRepo = new CourseRepo();

        $items = $courseRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => $item->id == $coupon->item_id,
            ];
        }

        return $result;
    }

    public function getXmPackages(CouponModel $coupon)
    {
        $packageRepo = new PackageRepo();

        $items = $packageRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => $item->id == $coupon->item_id,
            ];
        }

        return $result;
    }

    public function getXmVips(CouponModel $coupon)
    {
        $vipRepo = new VipRepo();

        $items = $vipRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->price),
                'value' => $item->id,
                'selected' => $item->id == $coupon->item_id,
            ];
        }

        return $result;
    }

    public function getXmExamPapers(CouponModel $coupon)
    {
        $paperRepo = new ExamPaperRepo();

        $items = $paperRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => $item->id == $coupon->item_id,
            ];
        }

        return $result;
    }

    public function getXmArticles(CouponModel $coupon)
    {
        $articleRepo = new ArticleRepo();

        $items = $articleRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => $item->id == $coupon->item_id,
            ];
        }

        return $result;
    }

    public function getCoupons()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $cardRepo = new CouponRepo();

        return $cardRepo->paginate($params, $sort, $page, $limit);
    }

    public function getCoupon($id)
    {
        return $this->findOrFail($id);
    }

    public function createCoupon()
    {
        $post = $this->request->getPost();

        $validator = new CouponValidator();

        $data = [];

        $data['name'] = $validator->checkName($post['name']);
        $data['type'] = $validator->checkType($post['type']);

        $coupon = new CouponModel();

        $coupon->assign($data);

        $coupon->create();

        return $coupon;
    }

    public function updateCoupon($id)
    {
        $coupon = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new CouponValidator();

        $data = [];

        if (isset($post['name'])) {
            $data['name'] = $validator->checkName($post['name']);
        }

        if (isset($post['type'])) {
            $data['type'] = $validator->checkType($post['type']);
            if ($data['type'] == CouponModel::TYPE_FIXED_AMOUNT) {
                $data['attrs'] = [
                    'deduct_amount' => $validator->checkDeductAmount($post['attrs']['deduct_amount']),
                ];
            } elseif ($data['type'] == CouponModel::TYPE_PERCENTAGE) {
                $data['attrs'] = [
                    'max_deduct_amount' => $validator->checkMaxDeductAmount($post['attrs']['max_deduct_amount']),
                    'discount_rate' => $validator->checkDiscountRate($post['discount_rate']),
                ];
            }
        }

        if (isset($post['consume_limit'])) {
            $data['consume_limit'] = $validator->checkConsumeLimit($post['consume_limit']);
        }

        if (isset($post['total_usage'])) {
            $data['total_usage'] = $validator->checkTotalUsage($post['total_usage']);
        }

        if (isset($post['user_usage'])) {
            $data['user_usage'] = $validator->checkUserUsage($post['user_usage']);
        }

        if (isset($post['start_time']) && isset($post['end_time'])) {
            $data['start_time'] = $validator->checkStartTime($post['start_time']);
            $data['end_time'] = $validator->checkEndTime($post['end_time']);
            $validator->checkTimeRange($data['start_time'], $data['end_time']);
        }

        if (isset($post['item_type'])) {
            $sync = new CouponSync();
            $data['item_type'] = $validator->checkItemType($post['item_type']);
            if ($post['item_type'] == KgSaleModel::ITEM_COURSE) {
                if (!empty($post['xm_course_id'])) {
                    $course = $validator->checkCourse($post['xm_course_id']);
                    $data['item_id'] = $course->id;
                    $data['item_info'] = $sync->getOriginCourseInfo($course);
                }
            } elseif ($post['item_type'] == KgSaleModel::ITEM_PACKAGE) {
                if (!empty($post['xm_package_id'])) {
                    $package = $validator->checkPackage($post['xm_package_id']);
                    $data['item_id'] = $package->id;
                    $data['item_info'] = $sync->getOriginPackageInfo($package);
                }
            } elseif ($post['item_type'] == KgSaleModel::ITEM_VIP) {
                if (!empty($post['xm_vip_id'])) {
                    $vip = $validator->checkVip($post['xm_vip_id']);
                    $data['item_id'] = $vip->id;
                    $data['item_info'] = $sync->getOriginVipInfo($vip);
                }
            } elseif ($post['item_type'] == KgSaleModel::ITEM_EXAM_PAPER) {
                if (!empty($post['xm_paper_id'])) {
                    $paper = $validator->checkExamPaper($post['xm_paper_id']);
                    $data['item_id'] = $paper->id;
                    $data['item_info'] = $sync->getOriginExamPaperInfo($paper);
                }
            } elseif ($post['item_type'] == KgSaleModel::ITEM_ARTICLE) {
                if (!empty($post['xm_article_id'])) {
                    $article = $validator->checkArticle($post['xm_article_id']);
                    $data['item_id'] = $article->id;
                    $data['item_info'] = $sync->getOriginArticleInfo($article);
                }
            }
        }

        if (isset($post['private'])) {
            $data['private'] = $validator->checkPrivateStatus($post['private']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        $coupon->assign($data);

        $coupon->update();

        return $coupon;
    }

    public function deleteCoupon($id)
    {
        $coupon = $this->findOrFail($id);

        $couponUserRepo = new CouponUserRepo();

        $couponUserRepo->deleteByCouponId($coupon->id);

        $coupon->deleted = 1;

        $coupon->update();

        return $coupon;
    }

    public function restoreCoupon($id)
    {
        $coupon = $this->findOrFail($id);

        $couponUserRepo = new CouponUserRepo();

        $couponUserRepo->restoreByCouponId($coupon->id);

        $coupon->deleted = 0;

        $coupon->update();

        return $coupon;
    }

    public function getCouponOrders($id)
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['promotion_id'] = $id;
        $params['promotion_type'] = OrderModel::PROMOTION_COUPON;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new OrderRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        if ($pager->total_items > 0) {

            $builder = new OrderListBuilder();

            $items = $pager->items->toArray();

            $pipeA = $builder->handleItems($items);
            $pipeB = $builder->handleUsers($pipeA);
            $pipeC = $builder->objects($pipeB);

            $pager->items = $pipeC;
        }

        return $pager;
    }

    protected function findOrFail($id)
    {
        $validator = new CouponValidator();

        return $validator->checkById($id);
    }

}
