<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\GroupArticleList as GroupArticleListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Group as GroupModel;
use App\Models\GroupArticle as GroupArticleModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Group as GroupRepo;
use App\Repos\GroupArticle as GroupArticleRepo;
use App\Validators\GroupArticle as GroupArticleValidator;

class GroupArticle extends Service
{

    public function create()
    {
        $post = $this->request->getPost();

        $validator = new GroupArticleValidator();

        $group = $validator->checkGroup($post['group_id']);

        $groupArticleRepo = new GroupArticleRepo();

        $articleIds = $post['xm_article_ids'] ? explode(',', $post['xm_article_ids']) : [];

        if (!$articleIds) return;

        foreach ($articleIds as $articleId) {

            $article = $validator->checkArticle($articleId);
            $groupArticle = $groupArticleRepo->findGroupArticle($group->id, $article->id);

            if (!$groupArticle) {
                $groupArticleModel = new GroupArticleModel();
                $groupArticleModel->group_id = $group->id;
                $groupArticleModel->article_id = $article->id;
                $groupArticleModel->create();
            }
        }

        $this->recountGroupArticles($group);
    }

    public function delete($id)
    {
        $validator = new GroupArticleValidator();

        $groupArticle = $validator->checkById($id);

        $group = $validator->checkGroup($groupArticle->group_id);

        $groupArticle->delete();

        $this->recountGroupArticles($group);
    }

    public function getArticles($id)
    {
        $validator = new GroupArticleValidator();

        $group = $validator->checkGroup($id);

        $pagerQuery = new PagerQuery();

        $params['group_id'] = $group->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GroupArticleRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleArticles($pager);
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
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => false,
            ];
        }

        return $result;
    }

    protected function recountGroupArticles(GroupModel $group)
    {
        $groupRepo = new GroupRepo();

        $articleCount = $groupRepo->countArticles($group->id);

        $group->article_count = $articleCount;

        $group->update();
    }

    protected function handleArticles($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new GroupArticleListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleArticles($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
