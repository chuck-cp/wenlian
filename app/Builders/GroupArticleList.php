<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\Article as ArticleRepo;
use App\Repos\Group as GroupRepo;

class GroupArticleList extends Builder
{

    public function handleGroups($relations)
    {
        $groups = $this->getGroups($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['group'] = $groups[$value['group_id']] ?? null;
        }

        return $relations;
    }

    public function handleArticles($relations)
    {
        $articles = $this->getArticles($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['article'] = $articles[$value['article_id']] ?? null;
        }

        return $relations;
    }

    public function getGroups($relations)
    {
        $ids = kg_array_column($relations, 'group_id');

        $groupRepo = new GroupRepo();

        $groups = $groupRepo->findShallowGroupByIds($ids);

        $result = [];

        foreach ($groups->toArray() as $group) {
            $result[$group['id']] = $group;
        }

        return $result;
    }

    public function getArticles($relations)
    {
        $ids = kg_array_column($relations, 'article_id');

        $articleRepo = new ArticleRepo();

        $articles = $articleRepo->findShallowArticleByIds($ids);

        $result = [];

        foreach ($articles->toArray() as $article) {
            $result[$article['id']] = $article;
        }

        return $result;
    }

}
