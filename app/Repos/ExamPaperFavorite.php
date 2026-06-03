<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamPaperFavorite as ExamPaperFavoriteModel;
use Phalcon\Mvc\Model;

class ExamPaperFavorite extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ExamPaperFavoriteModel::class);

        $builder->where('1 = 1');

        if (!empty($where['paper_id'])) {
            $builder->andWhere('paper_id = :paper_id:', ['paper_id' => $where['paper_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
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
     * @param int $paperId
     * @param int $userId
     * @return ExamPaperFavoriteModel|Model|bool
     */
    public function findExamPaperFavorite($paperId, $userId)
    {
        return ExamPaperFavoriteModel::findFirst([
            'conditions' => 'paper_id = :paper_id: AND user_id = :user_id:',
            'bind' => ['paper_id' => $paperId, 'user_id' => $userId],
        ]);
    }

}
