<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ArticleUserList as ArticleUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\ArticleUser as ArticleUserModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Services\Logic\Article\ArticleUserTrait;
use App\Validators\ArticleUser as ArticleUserValidator;

class ArticleUser extends Service
{

    use ArticleUserTrait;
    use AccountSearchTrait;

    public function getSourceTypes()
    {
        return ArticleUserModel::sourceTypes();
    }

    public function create()
    {
        $post = $this->request->getPost();

        $validator = new ArticleUserValidator();

        $article = $validator->checkArticle($post['article_id']);

        $user = $validator->checkUser($post['user_id']);

        $expiryTime = $validator->checkExpiryTime($post['expiry_time']);

        $sourceType = KgOwnershipModel::SOURCE_MANUAL;

        $this->assignUserArticle($article, $user, $expiryTime, $sourceType);
    }

    public function get($id)
    {
        $validator = new ArticleUserValidator();

        return $validator->checkById($id);
    }

    public function update($id)
    {
        $post = $this->request->getPost();

        $validator = new ArticleUserValidator();

        $articleUser = $validator->checkById($id);

        $articleUser->expiry_time = $validator->checkExpiryTime($post['expiry_time']);;

        $articleUser->update();
    }

    public function delete($id)
    {
        $validator = new ArticleUserValidator();

        $articleUser = $validator->checkById($id);

        $articleUser->deleted = 1;

        $articleUser->update();

        $article = $validator->checkArticle($articleUser->article_id);

        $this->recountArticleUsers($article);
    }

    public function getUsers($id)
    {
        $validator = new ArticleUserValidator();

        $article = $validator->checkArticle($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['article_id'] = $article->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new ArticleUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleUsers($pager);
    }

    protected function handleUsers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ArticleUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleUsers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
