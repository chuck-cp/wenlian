<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamQuestionFavorite as ExamQuestionFavoriteModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamQuestionFavorite extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->columns('qf.*');

        $builder->addFrom(ExamQuestionFavoriteModel::class,'qf');

        $builder->innerJoin(ExamQuestionModel::class, 'qf.question_id = q.id', 'q');

        $builder->where('1 = 1');

        if (!empty($where['model'])) {
            $builder->andWhere('q.model = :model:', ['model' => $where['model']]);
        }

        if (!empty($where['category_id'])) {
            $builder->andWhere('q.category_id = :category_id:', ['category_id' => $where['category_id']]);
        }

        if (!empty($where['question_id'])) {
            $builder->andWhere('qf.question_id = :question_id:', ['question_id' => $where['question_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('qf.user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('qf.deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'latest':
                $orderBy = 'qf.id DESC';
                break;
            default:
                $orderBy = 'qf.id ASC';
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
     * @param int $questionId
     * @param int $userId
     * @return ExamQuestionFavoriteModel|Model|bool
     */
    public function findExamQuestionFavorite($questionId, $userId)
    {
        return ExamQuestionFavoriteModel::findFirst([
            'conditions' => 'question_id = :question_id: AND user_id = :user_id:',
            'bind' => ['question_id' => $questionId, 'user_id' => $userId],
        ]);
    }

    /**
     * @param int $userId
     * @return ResultsetInterface|Resultset|ExamQuestionFavoriteModel[]
     */
    public function findByUserId($userId)
    {
        return ExamQuestionFavoriteModel::query()
            ->where('user_id = :user_id:', ['user_id' => $userId])
            ->andWhere('deleted = 0')
            ->execute();
    }

}
