<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Search;

use App\Library\Paginator\Adapter\XunSearch as XunSearchPaginator;
use App\Library\Paginator\Query as PagerQuery;
use App\Services\Search\ExamPaperSearcher as ExamPaperSearcherService;
use Phalcon\Text;

class ExamPaper extends Handler
{

    public function search()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $searcher = new ExamPaperSearcherService();

        $paginator = new XunSearchPaginator([
            'xs' => $searcher->getXS(),
            'highlight' => $searcher->getHighlightFields(),
            'query' => $this->handleKeywords($params['query']),
            'page' => $page,
            'limit' => $limit,
        ]);

        $pager = $paginator->paginate();

        return $this->handleExamPapers($pager);
    }

    public function getHotQuery($limit = 10, $type = 'total')
    {
        $searcher = new ExamPaperSearcherService();

        return $searcher->getHotQuery($limit, $type);
    }

    public function getRelatedQuery($query, $limit = 10)
    {
        $searcher = new ExamPaperSearcherService();

        return $searcher->getRelatedQuery($query, $limit);
    }

    protected function handleExamPapers($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $items = [];

        $baseUrl = kg_cos_url();

        foreach ($pager->items as $item) {

            $category = json_decode($item['category'], true);
            $tags = json_decode($item['tags'], true);

            if (!empty($item['cover']) && !Text::startsWith($item['cover'], 'http')) {
                $item['cover'] = $baseUrl . $item['cover'];
            }

            $items[] = [
                'id' => (int)$item['id'],
                'title' => (string)$item['title'],
                'cover' => (string)$item['cover'],
                'summary' => (string)$item['summary'],
                'level' => (int)$item['level'],
                'exam_type' => (int)$item['exam_type'],
                'pack_type' => (int)$item['pack_type'],
                'grade_type' => (int)$item['grade_type'],
                'market_price' => (float)$item['market_price'],
                'vip_price' => (float)$item['vip_price'],
                'question_count' => (int)$item['question_count'],
                'favorite_count' => (int)$item['favorite_count'],
                'join_count' => (int)$item['join_count'],
                'pass_count' => (int)$item['pass_count'],
                'category' => $category,
                'tags' => $tags,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
