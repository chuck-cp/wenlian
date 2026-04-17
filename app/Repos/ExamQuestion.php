<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamQuestion as ExamQuestionModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamQuestion extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ExamQuestionModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            if (is_array($where['id'])) {
                $builder->inWhere('id', $where['id']);
            } else {
                $builder->andWhere('id = :id:', ['id' => $where['id']]);
            }
        }

        if (!empty($where['category_id'])) {
            if (is_array($where['category_id'])) {
                $builder->inWhere('category_id', $where['category_id']);
            } else {
                $builder->andWhere('category_id = :category_id:', ['category_id' => $where['category_id']]);
            }
        }

        if (!empty($where['model'])) {
            if (is_array($where['model'])) {
                $builder->inWhere('model', $where['model']);
            } else {
                $builder->andWhere('model = :model:', ['model' => $where['model']]);
            }
        }

        if (!empty($where['level'])) {
            if (is_array($where['level'])) {
                $builder->inWhere('level', $where['level']);
            } else {
                $builder->andWhere('level = :level:', ['level' => $where['level']]);
            }
        }

        if (!empty($where['topic'])) {
            $builder->andWhere('topic LIKE :topic:', ['topic' => '%' . $where['topic'] . '%']);
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (isset($where['parent_id'])) {
            $builder->andWhere('parent_id = :parent_id:', ['parent_id' => $where['parent_id']]);
        }

        if (isset($where['featured'])) {
            $builder->andWhere('featured = :featured:', ['featured' => $where['featured']]);
        }

        if (isset($where['published'])) {
            $builder->andWhere('published = :published:', ['published' => $where['published']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        if ($sort == 'reported') {
            $builder->andWhere('report_count > 0');
        }

        switch ($sort) {
            case 'rand':
                $orderBy = 'RAND()';
                break;
            case 'model':
                $orderBy = 'model ASC';
                break;
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
     * @param array $categoryIds
     * @param int $limit
     * @return ResultsetInterface|Resultset|ExamQuestionModel[]
     */
    public function findByCategoryIds($categoryIds, $sort = 'latest', $limit = 10000)
    {
        $where = [
            'category_id' => $categoryIds,
            'published' => 1,
            'deleted' => 0,
        ];

        $paginate = $this->paginate($where, $sort, 1, $limit);

        return $paginate->items;
    }

    /**
     * @param array $where
     * @param int $limit
     * @return ResultsetInterface|Resultset|ExamQuestionModel[]
     */
    public function findByRand($where = [], $limit = 10)
    {
        $paginate = $this->paginate($where, 'rand', 1, $limit);

        return $paginate->items;
    }

    /**
     * @param int $id
     * @return ExamQuestionModel|Model|bool
     */
    public function findById($id)
    {
        return ExamQuestionModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param array $ids
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|ExamQuestionModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return ExamQuestionModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

    /**
     * @param array $ids
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|ExamQuestionModel[]
     */
    public function findByIdsWithModelOrder($ids, $columns = '*')
    {
        return ExamQuestionModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->orderBy('model ASC')
            ->execute();
    }

    /**
     * @param int $id
     * @return ResultsetInterface|Resultset|ExamQuestionModel[]
     */
    public function findChildQuestions($id)
    {
        return ExamQuestionModel::query()
            ->where('parent_id = :parent_id:', ['parent_id' => $id])
            ->andWhere('published = 1')
            ->andWhere('deleted = 0')
            ->orderBy('priority ASC, id ASC')
            ->execute();
    }

    public function sumParentScore($parentId)
    {
        return (int)ExamQuestionModel::sum([
            'column' => 'score',
            'conditions' => 'parent_id = :parent_id: AND published = 1 AND deleted = 0',
            'bind' => ['parent_id' => $parentId],
        ]);
    }

    public function maxChildPriority($parentId)
    {
        return (int)ExamQuestionModel::maximum([
            'column' => 'priority',
            'conditions' => 'parent_id = :parent_id: AND published = 1 AND deleted = 0',
            'bind' => ['parent_id' => $parentId],
        ]);
    }

}
