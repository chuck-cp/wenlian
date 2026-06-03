<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ArticleUser as ArticleUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ArticleUser extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ArticleUserModel::class);

        $builder->where('1 = 1');

        if (!empty($where['article_id'])) {
            $builder->andWhere('article_id = :article_id:', ['article_id' => $where['article_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['source_type'])) {
            if (is_array($where['source_type'])) {
                $builder->inWhere('source_type', $where['source_type']);
            } else {
                $builder->andWhere('source_type = :source_type:', ['source_type' => $where['source_type']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (!empty($where['expiry_time'][0]) && !empty($where['expiry_time'][1])) {
            $startTime = strtotime($where['expiry_time'][0]);
            $endTime = strtotime($where['expiry_time'][1]);
            $builder->betweenWhere('expiry_time', $startTime, $endTime);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
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
     * @return ArticleUserModel|Model|bool
     */
    public function findById($id)
    {
        return ArticleUserModel::findFirst($id);
    }

    /**
     * @param int $articleId
     * @param int $userId
     * @return ArticleUserModel|Model|bool
     */
    public function findArticleUser($articleId, $userId)
    {
        return ArticleUserModel::findFirst([
            'conditions' => 'article_id = ?1 AND user_id = ?2 AND deleted = 0',
            'bind' => [1 => $articleId, 2 => $userId],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $articleId
     * @param int $userId
     * @return ResultsetInterface|Resultset|ArticleUserModel[]
     */
    public function findByArticleAndUserId($articleId, $userId)
    {
        return ArticleUserModel::query()
            ->where('article_id = :article_id:', ['article_id' => $articleId])
            ->andWhere('user_id = :user_id:', ['user_id' => $userId])
            ->execute();
    }

}
