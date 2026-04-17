<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Caches\Distribution as DistributionCache;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Distribution as DistributionModel;
use App\Models\KgSale as KgSaleModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\Distribution as DistributionRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\Vip as VipRepo;
use App\Validators\Distribution as DistributionValidator;

class Distribution extends Service
{

    public function getItemTypes()
    {
        return DistributionModel::itemTypes();
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
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
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
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
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
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->price),
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

    public function getDistributions()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleSearchParams($params);

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new DistributionRepo();

        return $repo->paginate($params, $sort, $page, $limit);
    }

    public function getDistribution($id)
    {
        return $this->findOrFail($id);
    }

    public function createDistribution()
    {
        $post = $this->request->getPost();

        $data = [];

        $validator = new DistributionValidator();

        $data['item_type'] = $validator->checkItemType($post['item_type']);
        $data['v1_com_rate'] = $validator->checkV1ComRate($post['v1_com_rate']);
        $data['v2_com_rate'] = $validator->checkV2ComRate($post['v2_com_rate']);
        $data['v3_com_rate'] = $validator->checkV3ComRate($post['v3_com_rate']);
        $data['start_time'] = $validator->checkStartTime($post['start_time']);
        $data['end_time'] = $validator->checkEndTime($post['end_time']);

        $validator->checkTimeRange($data['start_time'], $data['end_time']);

        if ($post['item_type'] == KgSaleModel::ITEM_COURSE) {

            $data['item_ids'] = $validator->checkItemIds($post['xm_course_id']);

            $this->createCourseDistributions($data);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_PACKAGE) {

            $data['item_ids'] = $validator->checkItemIds($post['xm_package_id']);

            $this->createPackageDistributions($data);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_VIP) {

            $data['item_ids'] = $validator->checkItemIds($post['xm_vip_id']);

            $this->createVipDistributions($data);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_EXAM_PAPER) {

            $data['item_ids'] = $validator->checkItemIds($post['xm_paper_id']);

            $this->createExamPaperDistributions($data);

        } elseif ($post['item_type'] == KgSaleModel::ITEM_ARTICLE) {

            $data['item_ids'] = $validator->checkItemIds($post['xm_article_id']);

            $this->createArticleDistributions($data);
        }
    }

    public function updateDistribution($id)
    {
        $distribution = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new DistributionValidator();

        $data = [];

        if (isset($post['v1_com_rate'])) {
            $data['v1_com_rate'] = $validator->checkV1ComRate($post['v1_com_rate']);
        }

        if (isset($post['v2_com_rate'])) {
            $data['v2_com_rate'] = $validator->checkV2ComRate($post['v2_com_rate']);
        }

        if (isset($post['v3_com_rate'])) {
            $data['v3_com_rate'] = $validator->checkV3ComRate($post['v3_com_rate']);
        }

        if (isset($post['start_time']) && isset($post['end_time'])) {
            $data['start_time'] = $validator->checkStartTime($post['start_time']);
            $data['end_time'] = $validator->checkEndTime($post['end_time']);
            $validator->checkTimeRange($data['start_time'], $data['end_time']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        $distribution->assign($data);

        $distribution->update();

        return $distribution;
    }

    public function deleteDistribution($id)
    {
        $distribution = $this->findOrFail($id);

        $distribution->deleted = 1;

        $distribution->update();

        return $distribution;
    }

    public function restoreDistribution($id)
    {
        $distribution = $this->findOrFail($id);

        $distribution->deleted = 0;

        $distribution->update();

        return $distribution;
    }

    protected function findOrFail($id)
    {
        $validator = new DistributionValidator();

        return $validator->checkDistribution($id);
    }

    protected function createCourseDistributions($data)
    {
        $courseRepo = new CourseRepo();

        $courses = $courseRepo->findByIds($data['item_ids']);

        $excludeIds = $this->getExcludeItemIds($data['item_type'], $data['item_ids']);

        $sync = new DistributionSync();

        foreach ($courses as $course) {
            if (!in_array($course->id, $excludeIds)) {
                $distribution = new DistributionModel();
                $distribution->item_id = $course->id;
                $distribution->item_info = $sync->getOriginCourseInfo($course);
                $distribution->item_type = $data['item_type'];
                $distribution->v1_com_rate = $data['v1_com_rate'];
                $distribution->v2_com_rate = $data['v2_com_rate'];
                $distribution->v3_com_rate = $data['v3_com_rate'];
                $distribution->start_time = $data['start_time'];
                $distribution->end_time = $data['end_time'];
                $distribution->create();
            }
        }
    }

    protected function createPackageDistributions($data)
    {
        $packageRepo = new PackageRepo();

        $packages = $packageRepo->findByIds($data['item_ids']);

        $excludeIds = $this->getExcludeItemIds($data['item_type'], $data['item_ids']);

        $sync = new DistributionSync();

        foreach ($packages as $package) {
            if (!in_array($package->id, $excludeIds)) {
                $distribution = new DistributionModel();
                $distribution->item_id = $package->id;
                $distribution->item_info = $sync->getOriginPackageInfo($package);
                $distribution->item_type = $data['item_type'];
                $distribution->v1_com_rate = $data['v1_com_rate'];
                $distribution->v2_com_rate = $data['v2_com_rate'];
                $distribution->v3_com_rate = $data['v3_com_rate'];
                $distribution->start_time = $data['start_time'];
                $distribution->end_time = $data['end_time'];
                $distribution->create();
            }
        }
    }

    protected function createVipDistributions($data)
    {
        $vipRepo = new VipRepo();

        $vips = $vipRepo->findByIds($data['item_ids']);

        $excludeIds = $this->getExcludeItemIds($data['item_type'], $data['item_ids']);

        $sync = new DistributionSync();

        foreach ($vips as $vip) {
            if (!in_array($vip->id, $excludeIds)) {
                $distribution = new DistributionModel();
                $distribution->item_id = $vip->id;
                $distribution->item_info = $sync->getOriginVipInfo($vip);
                $distribution->item_type = $data['item_type'];
                $distribution->v1_com_rate = $data['v1_com_rate'];
                $distribution->v2_com_rate = $data['v2_com_rate'];
                $distribution->v3_com_rate = $data['v3_com_rate'];
                $distribution->start_time = $data['start_time'];
                $distribution->end_time = $data['end_time'];
                $distribution->create();
            }
        }
    }

    protected function createExamPaperDistributions($data)
    {
        $paperRepo = new ExamPaperRepo();

        $papers = $paperRepo->findByIds($data['item_ids']);

        $excludeIds = $this->getExcludeItemIds($data['item_type'], $data['item_ids']);

        $sync = new DistributionSync();

        foreach ($papers as $paper) {
            if (!in_array($paper->id, $excludeIds)) {
                $distribution = new DistributionModel();
                $distribution->item_id = $paper->id;
                $distribution->item_info = $sync->getOriginExamPaperInfo($paper);
                $distribution->item_type = $data['item_type'];
                $distribution->v1_com_rate = $data['v1_com_rate'];
                $distribution->v2_com_rate = $data['v2_com_rate'];
                $distribution->v3_com_rate = $data['v3_com_rate'];
                $distribution->start_time = $data['start_time'];
                $distribution->end_time = $data['end_time'];
                $distribution->create();
            }
        }
    }

    protected function createArticleDistributions($data)
    {
        $articleRepo = new ArticleRepo();

        $articles = $articleRepo->findByIds($data['item_ids']);

        $excludeIds = $this->getExcludeItemIds($data['item_type'], $data['item_ids']);

        $sync = new DistributionSync();

        foreach ($articles as $article) {
            if (!in_array($article->id, $excludeIds)) {
                $distribution = new DistributionModel();
                $distribution->item_id = $article->id;
                $distribution->item_info = $sync->getOriginArticleInfo($article);
                $distribution->item_type = $data['item_type'];
                $distribution->v1_com_rate = $data['v1_com_rate'];
                $distribution->v2_com_rate = $data['v2_com_rate'];
                $distribution->v3_com_rate = $data['v3_com_rate'];
                $distribution->start_time = $data['start_time'];
                $distribution->end_time = $data['end_time'];
                $distribution->create();
            }
        }
    }

    protected function getExcludeItemIds($itemType, $itemIds)
    {
        $itemRepo = new DistributionRepo();

        $distributions = $itemRepo->findByItemIds($itemType, $itemIds);

        $excludeIds = [];

        if ($distributions->count() > 0) {
            foreach ($distributions as $distribution) {
                if ($distribution->deleted == 0 && $distribution->end_time > time()) {
                    $excludeIds[] = $distribution->item_id;
                }
            }
        }

        return $excludeIds;
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
            } elseif ($params['item_type'] == KgSaleModel::ITEM_ARTICLE) {
                $itemId = $params['xm_article_id'] ?? null;
            }
        }

        if (!empty($itemId)) {
            $params['item_id'] = $itemId;
        }

        $params['deleted'] = $params['deleted'] ?? 0;

        return $params;
    }

}
