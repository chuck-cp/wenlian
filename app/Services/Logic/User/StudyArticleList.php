<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User;

use App\Builders\ArticleUserList as ArticleUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;

class StudyArticleList extends LogicService
{

    use UserTrait;

    public function handle($id)
    {
        $user = $this->checkUserCache($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new ArticleUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new ArticleUserListBuilder();

        $relations = $pager->items->toArray();

        $articles = $builder->getArticles($relations);

        $items = [];

        foreach ($relations as $relation) {

            $article = $articles[$relation['article_id']] ?? new \stdClass();

            $items[] = [
                'id' => $relation['id'],
                'source_type' => $relation['source_type'],
                'expiry_time' => $relation['expiry_time'],
                'create_time' => $relation['create_time'],
                'article' => $article,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
