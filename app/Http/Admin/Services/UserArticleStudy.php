<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ArticleUserList as ArticleUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Services\Logic\Article\ArticleUserTrait;
use App\Validators\ArticleUser as ArticleUserValidator;

class UserArticleStudy extends Service
{

    use ArticleUserTrait;

    public function getArticles($id)
    {
        $validator = new ArticleUserValidator();

        $user = $validator->checkUser($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $articleUserRepo = new ArticleUserRepo();

        $pager = $articleUserRepo->paginate($params, $sort, $page, $limit);

        return $this->handleArticles($pager);
    }

    protected function handleArticles($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ArticleUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleArticles($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
