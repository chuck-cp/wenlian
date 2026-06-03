<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamPaperQuestion as ExamPaperQuestionModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamPaperQuestion extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ExamPaperQuestionModel::class);

        $builder->where('1 = 1');

        if (!empty($where['paper_id'])) {
            $builder->andWhere('paper_id = :paper_id:', ['paper_id' => $where['paper_id']]);
        }

        if (!empty($where['question_id'])) {
            $builder->andWhere('question_id = :question_id:', ['question_id' => $where['question_id']]);
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
     * @return ResultsetInterface|Resultset|ExamPaperQuestionModel[]
     */
    public function findByPaperId($paperId)
    {
        return ExamPaperQuestionModel::query()
            ->where('paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('deleted = 0')
            ->orderBy('priority ASC')
            ->execute();
    }

    /**
     * @param int $paperId
     * @param int $questionId
     * @return ExamPaperQuestionModel|Model|bool
     */
    public function findPaperQuestion($paperId, $questionId)
    {
        return ExamPaperQuestionModel::findFirst([
            'conditions' => 'paper_id = ?1 AND question_id = ?2',
            'bind' => [1 => $paperId, 2 => $questionId],
            'order' => 'id DESC',
        ]);
    }

}
