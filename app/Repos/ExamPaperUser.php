<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamPaperUser extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->addFrom(ExamPaperUserModel::class, 'pu');

        $builder->innerJoin(ExamPaperModel::class, 'pu.paper_id = p.id', 'p');

        $builder->where('1 = 1');

        if (!empty($where['exam_type'])) {
            $builder->andWhere('p.exam_type = :exam_type:', ['exam_type' => $where['exam_type']]);
        }

        if (!empty($where['paper_id'])) {
            $builder->andWhere('pu.paper_id = :paper_id:', ['paper_id' => $where['paper_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('pu.user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['source_type'])) {
            if (is_array($where['source_type'])) {
                $builder->inWhere('pu.source_type', $where['source_type']);
            } else {
                $builder->andWhere('pu.source_type = :source_type:', ['source_type' => $where['source_type']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('pu.create_time', $startTime, $endTime);
        }

        if (!empty($where['expiry_time'][0]) && !empty($where['expiry_time'][1])) {
            $startTime = strtotime($where['expiry_time'][0]);
            $endTime = strtotime($where['expiry_time'][1]);
            $builder->betweenWhere('expiry_time', $startTime, $endTime);
        }

        if (!empty($where['status'])) {
            if (is_array($where['status'])) {
                $builder->inWhere('pu.status', $where['status']);
            } else {
                $builder->andWhere('pu.status = :status:', ['status' => $where['status']]);
            }
        }

        if (isset($where['debut'])) {
            $builder->andWhere('pu.debut = :debut:', ['debut' => $where['debut']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('pu.deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'oldest':
                $orderBy = 'pu.id ASC';
                break;
            default:
                $orderBy = 'pu.id DESC,pu.debut DESC';
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
     * @return ExamPaperUserModel|Model|bool
     */
    public function findById($id)
    {
        return ExamPaperUserModel::findFirst($id);
    }

    /**
     * @param int $paperId
     * @param int $userId
     * @return ExamPaperUserModel|Model|bool
     */
    public function findDebutPaperUser($paperId, $userId)
    {
        return ExamPaperUserModel::findFirst([
            'conditions' => 'paper_id = ?1 AND user_id = ?2 AND debut = 1 AND deleted = 0',
            'bind' => [1 => $paperId, 2 => $userId],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param int $paperId
     * @param int $userId
     * @return ResultsetInterface|Resultset|ExamPaperUserModel[]
     */
    public function findByPaperAndUserId($paperId, $userId)
    {
        return ExamPaperUserModel::query()
            ->where('paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('user_id = :user_id:', ['user_id' => $userId])
            ->execute();
    }

}
