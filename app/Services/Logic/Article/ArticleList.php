<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Article;

use App\Builders\ArticleList as ArticleListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Article as ArticleRepo;
use App\Services\Category as CategoryService;
use App\Services\Logic\Service as LogicService;
use App\Validators\ArticleQuery as ArticleQueryValidator;
use Phalcon\Text;

class ArticleList extends LogicService
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

        $articleRepo = new ArticleRepo();

        $pager = $articleRepo->paginate($params, $sort, $page, $limit);

        return $this->handleArticles($pager);
    }

    public function handleArticles($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new ArticleListBuilder();

        $categories = $builder->getCategories();

        $articles = $pager->items->toArray();

        $users = $builder->getUsers($articles);

        $items = [];

        $cosUrl = kg_cos_url();

        foreach ($articles as $article) {

            $article['tags'] = json_decode($article['tags'], true);

            if ($article['fake_user_count'] > $article['user_count']) {
                $article['user_count'] = $article['fake_user_count'];
            }

            if (empty($article['cover'])) {
                $article['cover'] = kg_default_article_cover_path();
            }

            if (!Text::startsWith($article['cover'], 'http')) {
                $article['cover'] = $cosUrl . $article['cover'];
            }

            $category = $categories[$article['category_id']] ?? new \stdClass();

            $owner = $users[$article['owner_id']] ?? new \stdClass();

            $items[] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'cover' => $article['cover'],
                'summary' => $article['summary'],
                'images' => $article['images'],
                'tags' => $article['tags'],
                'featured' => $article['featured'],
                'published' => $article['published'],
                'deleted' => $article['deleted'],
                'market_price' => $article['market_price'],
                'vip_price' => $article['vip_price'],
                'source_type' => $article['source_type'],
                'user_count' => $article['user_count'],
                'view_count' => $article['view_count'],
                'like_count' => $article['like_count'],
                'comment_count' => $article['comment_count'],
                'favorite_count' => $article['favorite_count'],
                'create_time' => $article['create_time'],
                'update_time' => $article['update_time'],
                'category' => $category,
                'owner' => $owner,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

    protected function checkQueryParams($params)
    {
        $validator = new ArticleQueryValidator();

        $query = [];

        if (isset($params['owner_id'])) {
            $user = $validator->checkUser($params['owner_id']);
            $query['owner_id'] = $user->id;
        }

        if (isset($params['tag_id'])) {
            $tag = $validator->checkTag($params['tag_id']);
            $query['tag_id'] = $tag->id;
        }

        if (isset($params['source_type'])) {
            $query['source_type'] = $validator->checkSourceType($params['source_type']);
        }

        if (isset($params['tc'])) {
            $category = $validator->checkCategory($params['tc']);
            $query['tc'] = $category->id;
        }

        if (isset($params['sc'])) {
            $category = $validator->checkCategory($params['sc']);
            $query['sc'] = $category->id;
        }

        if (isset($params['sort'])) {
            $query['sort'] = $validator->checkSort($params['sort']);
        }

        return $query;
    }

}
