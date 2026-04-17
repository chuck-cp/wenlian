<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\GroupArticle as GroupArticleModel;
use Phalcon\Mvc\Model;

class GroupArticle extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(GroupArticleModel::class);

        $builder->where('1 = 1');

        if (!empty($where['group_id'])) {
            $builder->andWhere('group_id = :group_id:', ['group_id' => $where['group_id']]);
        }

        if (!empty($where['article_id'])) {
            $builder->andWhere('article_id = :article_id:', ['article_id' => $where['article_id']]);
        }

        switch ($sort) {
            case 'oldest':
                $orderBy = 'id ASC';
                break;
            default:
                $orderBy = 'id DESC';
                break;
        }

        $builder->orderBy($orderBy);

        $pager = new PagerQueryBuilder([
            'builder' => $builder,
            'page' => $page,
            'limit' => $limit,
        ]);

        return $pager->paginate();
    }

    /**
     * @param int $id
     * @return GroupArticleModel|Model|bool
     */
    public function findById($id)
    {
        return GroupArticleModel::findFirst($id);
    }

    /**
     * @param int $groupId
     * @param int $articleId
     * @return GroupArticleModel|Model|bool
     */
    public function findGroupArticle($groupId, $articleId)
    {
        return GroupArticleModel::findFirst([
            'conditions' => 'group_id = ?1 AND article_id = ?2',
            'bind' => [1 => $groupId, 2 => $articleId],
            'order' => 'id DESC',
        ]);
    }

}
