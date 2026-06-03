<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Library\Paginator\Query as PagerQuery;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Services\Category as CategoryService;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperQuery as ExamPaperQueryValidator;

class PaperList extends LogicService
{

    public function handle()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->checkQueryParams($params);

        /**
         * tc => top_category
         * sc => sub_category
         */
        if (!empty($params['sc'])) {

            $params['category_id'] = $params['sc'];

        } elseif (!empty($params['tc'])) {

            $categoryService = new CategoryService();

            $childCategoryIds = $categoryService->getChildCategoryIds($params['tc']);

            $parentCategoryIds = [$params['tc']];

            $allCategoryIds = array_merge($parentCategoryIds, $childCategoryIds);

            $params['category_id'] = $allCategoryIds;
        }

        $params['published'] = 1;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $paperRepo = new ExamPaperRepo();

        $pager = $paperRepo->paginate($params, $sort, $page, $limit);

        return $this->handlePapers($pager);
    }

    public function handlePapers($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $papers = $pager->items->toArray();

        $items = [];

        $baseUrl = kg_cos_url();

        foreach ($papers as $paper) {

            $paper['cover'] = $baseUrl . $paper['cover'];

            if ($paper['fake_join_count'] > $paper['join_count']) {
                $paper['join_count'] = $paper['fake_join_count'];
            }

            $items[] = [
                'id' => $paper['id'],
                'title' => $paper['title'],
                'cover' => $paper['cover'],
                'level' => $paper['level'],
                'duration' => $paper['duration'],
                'featured' => $paper['featured'],
                'published' => $paper['published'],
                'deleted' => $paper['deleted'],
                'exam_type' => $paper['exam_type'],
                'pack_type' => $paper['pack_type'],
                'market_price' => (float)$paper['market_price'],
                'vip_price' => (float)$paper['vip_price'],
                'question_count' => $paper['question_count'],
                'favorite_count' => $paper['favorite_count'],
                'join_count' => $paper['join_count'],
                'pass_count' => $paper['pass_count'],
            ];
        }

        $pager->items = $items;

        return $pager;
    }

    protected function checkQueryParams($params)
    {
        $validator = new ExamPaperQueryValidator();

        $query = [];

        if (isset($params['tag_id'])) {
            $tag = $validator->checkTag($params['tag_id']);
            $query['tag_id'] = $tag->id;
        }

        if (isset($params['exam_type'])) {
            $query['exam_type'] = $validator->checkExamType($params['exam_type']);
        }

        if (isset($params['pack_type'])) {
            $query['pack_type'] = $validator->checkPackType($params['pack_type']);
        }

        if (isset($params['tc'])) {
            $category = $validator->checkCategory($params['tc']);
            $query['tc'] = $category->id;
        }

        if (isset($params['sc'])) {
            $category = $validator->checkCategory($params['sc']);
            $query['sc'] = $category->id;
        }

        if (isset($params['level'])) {
            $query['level'] = $validator->checkLevel($params['level']);
        }

        if (isset($params['sort'])) {
            $query['sort'] = $validator->checkSort($params['sort']);
        }

        return $query;
    }

}
