<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamQuestionUser extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ExamQuestionUserModel::class);

        $builder->where('1 = 1');

        if (!empty($where['paper_user_id'])) {
            $builder->andWhere('paper_user_id = :paper_user_id:', ['paper_user_id' => $where['paper_user_id']]);
        }

        if (!empty($where['paper_id'])) {
            $builder->andWhere('paper_id = :paper_id:', ['paper_id' => $where['paper_id']]);
        }

        if (!empty($where['question_id'])) {
            $builder->andWhere('question_id = :question_id:', ['question_id' => $where['question_id']]);
        }

        if (!empty($where['question_model'])) {
            if (is_array($where['question_model'])) {
                $builder->inWhere('question_model', $where['question_model']);
            } else {
                $builder->andWhere('question_model = :question_model:', ['question_model' => $where['question_model']]);
            }
        }

        if (!empty($where['question_parent_id'])) {
            $builder->andWhere('question_parent_id = :question_parent_id:', ['question_parent_id' => $where['question_parent_id']]);
        } else {
            $builder->andWhere('question_parent_id = 0');
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (isset($where['finished'])) {
            $builder->andWhere('finished = :finished:', ['finished' => $where['finished']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'latest':
                $orderBy = 'id DESC';
                break;
            default:
                $orderBy = 'id ASC';
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
     * @param int $paperUserId
     * @param int $questionId
     * @param int $userId
     * @return ExamQuestionUserModel|Model|bool
     */
    public function findExamQuestionUser($paperUserId, $questionId, $userId)
    {
        return ExamQuestionUserModel::findFirst([
            'conditions' => 'paper_user_id = ?1 AND question_id = ?2 AND user_id = ?3',
            'bind' => [1 => $paperUserId, 2 => $questionId, 3 => $userId],
        ]);
    }

    /**
     * @param int $questionId
     * @param int $userId
     * @return ExamQuestionUserModel|Model|bool
     */
    public function findFinishedQuestionUser($questionId, $userId)
    {
        return ExamQuestionUserModel::findFirst([
            'conditions' => 'question_id = ?1 AND user_id = ?2 AND finished = 1',
            'bind' => [1 => $questionId, 2 => $userId],
        ]);
    }

    /**
     * @param int $paperUserId
     * @return ResultsetInterface|Resultset|ExamQuestionUserModel[]
     */
    public function findByPaperUserId($paperUserId)
    {
        return ExamQuestionUserModel::query()
            ->where('paper_user_id = :paper_user_id:', ['paper_user_id' => $paperUserId])
            ->execute();
    }

    public function undoFinishedByPaperUserId($paperUserId)
    {
        $phql = sprintf('UPDATE %s SET finished = 0 WHERE paper_user_id = %s', ExamQuestionUserModel::class, $paperUserId);

        $this->modelsManager->executeQuery($phql);
    }

}
