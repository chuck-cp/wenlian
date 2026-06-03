<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\ExamQuestionMistake as ExamQuestionMistakeModel;
use Phalcon\Mvc\Model;

class ExamQuestionMistake extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->columns('qm.*');

        $builder->addFrom(ExamQuestionMistakeModel::class, 'qm');

        $builder->innerJoin(ExamQuestionModel::class, 'qm.question_id = q.id', 'q');

        $builder->where('1 = 1');

        if (!empty($where['model'])) {
            $builder->andWhere('q.model = :model:', ['model' => $where['model']]);
        }

        if (!empty($where['category_id'])) {
            $builder->andWhere('q.category_id = :category_id:', ['category_id' => $where['category_id']]);
        }

        if (!empty($where['question_id'])) {
            $builder->andWhere('qm.question_id = :question_id:', ['question_id' => $where['question_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('qm.user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('qm.deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'oldest':
                $orderBy = 'qm.id ASC';
                break;
            default:
                $orderBy = 'qm.id DESC';
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
     * @return ExamQuestionMistakeModel|Model|bool
     */
    public function findExamQuestionMistake($questionId, $userId)
    {
        return ExamQuestionMistakeModel::findFirst([
            'conditions' => 'question_id = :question_id: AND user_id = :user_id:',
            'bind' => ['question_id' => $questionId, 'user_id' => $userId],
        ]);
    }

}
