<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\FlashSale as FlashSaleModel;
use App\Models\KgSale as KgSaleModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\FlashSale as FlashSaleRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\Vip as VipRepo;
use App\Services\Logic\FlashSale\Queue as FlashSaleQueue;
use App\Validators\FlashSale as FlashSaleValidator;

class FlashSale extends Service
{

    public function getItemTypes()
    {
        return FlashSaleModel::itemTypes();
    }

    public function getXmSchedules($id)
    {
        $schedules = FlashSaleModel::schedules();

        $sale = $this->findOrFail($id);

        $result = [];

        foreach ($schedules as $schedule) {
            $result[] = [
                'name' => $schedule['name'],
                'value' => $schedule['hour'],
                'selected' => in_array($schedule['hour'], $sale->schedules),
            ];
        }

        return $result;
    }

    public function getXmCourses()
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
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmPackages()
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
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmVips()
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
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmExamPapers()
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
            ];
        }

        return $result;
    }

    public function getXmArticles()
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
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getFlashSales()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleSearchParams($params);

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $saleRepo = new FlashSaleRepo();

        return $saleRepo->paginate($params, $sort, $page, $limit);
    }

    public function getFlashSale($id)
    {
        return $this->findOrFail($id);
    }

    public function createFlashSale()
    {
        $post = $this->request->getPost();

        $sync = new FlashSaleSync();

        $validator = new FlashSaleValidator();

        $data = [];

        $data['item_type'] = $validator->checkItemType($post['item_type']);

        if ($post['item_type'] == KgSaleModel::ITEM_COURSE) {

            $course = $validator->checkCourse($post['xm_course_id']);

            $data['item_id'] = $course->id;
            $data['item_info'] = $sync->getOriginCourseInfo($course);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_PACKAGE) {

            $package = $validator->checkPackage($post['xm_package_id']);

            $data['item_id'] = $package->id;
            $data['item_info'] = $sync->getOriginPackageInfo($package);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_VIP) {

            $vip = $validator->checkVip($post['xm_vip_id']);

            $data['item_id'] = $vip->id;
            $data['item_info'] = $sync->getOriginVipInfo($vip);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_EXAM_PAPER) {

            $examPaper = $validator->checkExamPaper($post['xm_paper_id']);

            $data['item_id'] = $examPaper->id;
            $data['item_info'] = $sync->getOriginExamPaperInfo($examPaper);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_ARTICLE) {

            $article = $validator->checkArticle($post['xm_article_id']);

            $data['item_id'] = $article->id;
            $data['item_info'] = $sync->getOriginArticleInfo($article);
        }

        $validator->checkIfActiveItemExisted($data['item_id'], $data['item_type']);

        $sale = new FlashSaleModel();

        $sale->assign($data);

        $sale->create();

        return $sale;
    }

    public function updateFlashSale($id)
    {
        $sale = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new FlashSaleValidator();

        $data = [];

        if (isset($post['start_time']) && isset($post['end_time'])) {
            $data['start_time'] = $validator->checkStartTime($post['start_time']);
            $data['end_time'] = $validator->checkEndTime($post['end_time']);
            $validator->checkTimeRange($data['start_time'], $data['end_time']);
        }

        if (isset($post['xm_schedules'])) {
            $data['schedules'] = $validator->checkSchedules($post['xm_schedules']);
        }

        if (isset($post['stock'])) {
            $data['stock'] = $validator->checkStock($post['stock']);
        }

        if (isset($post['price'])) {
            $data['price'] = $validator->checkPrice($post['price']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        $sale->assign($data);

        $sale->update();

        $this->initFlashSaleQueue($sale);

        return $sale;
    }

    public function deleteFlashSale($id)
    {
        $sale = $this->findOrFail($id);

        $sale->deleted = 1;

        $sale->update();

        return $sale;
    }

    public function restoreFlashSale($id)
    {
        $sale = $this->findOrFail($id);

        $sale->deleted = 0;

        $sale->update();

        return $sale;
    }

    protected function findOrFail($id)
    {
        $validator = new FlashSaleValidator();

        return $validator->checkFlashSale($id);
    }

    protected function initFlashSaleQueue(FlashSaleModel $sale)
    {
        $queue = new FlashSaleQueue();

        $queue->init($sale->id);
    }

    protected function handleSearchParams($params)
    {
        $itemId = null;

        if (!empty($params['item_type'])) {
            if ($params['item_type'] == KgSaleModel::ITEM_COURSE) {
                $itemId = $params['xm_course_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_PACKAGE) {
                $itemId = $params['xm_package_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_VIP) {
                $itemId = $params['xm_vip_id'] ?? null;
            } elseif ($params['item_type'] == KgSaleModel::ITEM_EXAM_PAPER) {
                $itemId = $params['xm_paper_id'] ?? null;
            }
        }

        if (!empty($itemId)) {
            $params['item_id'] = $itemId;
        }

        $params['deleted'] = $params['deleted'] ?? 0;

        return $params;
    }

}
