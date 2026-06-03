<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperFavorite as ExamPaperFavoriteModel;
use App\Models\ExamPaperQuestion as ExamPaperQuestionModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamPaper extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ExamPaperModel::class);

        $builder->where('1 = 1');

        $fakeId = false;

        if (!empty($where['tag_id'])) {
            $where['id'] = $this->getTaggedPaperIds($where['tag_id']);
            $fakeId = empty($where['id']);
        }

        /**
         * 构造空记录条件
         */
        if ($fakeId) $where['id'] = -999;

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

        if (!empty($where['teacher_id'])) {
            if (is_array($where['teacher_id'])) {
                $builder->inWhere('teacher_id', $where['teacher_id']);
            } else {
                $builder->andWhere('teacher_id = :teacher_id:', ['teacher_id' => $where['teacher_id']]);
            }
        }

        if (!empty($where['exam_type'])) {
            if (is_array($where['exam_type'])) {
                $builder->inWhere('exam_type', $where['exam_type']);
            } else {
                $builder->andWhere('exam_type = :exam_type:', ['exam_type' => $where['exam_type']]);
            }
        }

        if (!empty($where['pack_type'])) {
            if (is_array($where['pack_type'])) {
                $builder->inWhere('pack_type', $where['pack_type']);
            } else {
                $builder->andWhere('pack_type = :pack_type:', ['pack_type' => $where['pack_type']]);
            }
        }

        if (!empty($where['grade_type'])) {
            if (is_array($where['grade_type'])) {
                $builder->inWhere('grade_type', $where['grade_type']);
            } else {
                $builder->andWhere('grade_type = :grade_type:', ['grade_type' => $where['grade_type']]);
            }
        }

        if (!empty($where['level'])) {
            if (is_array($where['level'])) {
                $builder->inWhere('level', $where['model']);
            } else {
                $builder->andWhere('level = :level:', ['level' => $where['level']]);
            }
        }

        if (!empty($where['title'])) {
            $builder->andWhere('title LIKE :title:', ['title' => "%{$where['title']}%"]);
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (isset($where['free'])) {
            if ($where['free'] == 1) {
                $builder->andWhere('market_price = 0');
            } else {
                $builder->andWhere('market_price > 0');
            }
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

        if ($sort == 'free') {
            $builder->andWhere('market_price = 0');
        } elseif ($sort == 'featured') {
            $builder->andWhere('featured = 1');
        } elseif ($sort == 'vip_discount') {
            $builder->andWhere('vip_price < market_price');
            $builder->andWhere('vip_price > 0');
        } elseif ($sort == 'vip_free') {
            $builder->andWhere('market_price > 0');
            $builder->andWhere('vip_price = 0');
        }

        switch ($sort) {
            case 'popular':
                $orderBy = 'join_count DESC, id DESC';
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
     * @param array $where
     * @param string $sort
     * @return ResultsetInterface|Resultset|ExamPaperModel[]
     */
    public function findAll($where = [], $sort = 'latest', $limit = 10000)
    {
        /**
         * 一个偷懒的实现，适用于中小体量数据
         */
        $paginate = $this->paginate($where, $sort, 1, $limit);

        return $paginate->items;
    }

    /**
     * @param int $id
     * @return ExamPaperModel|Model|bool
     */
    public function findById($id)
    {
        return ExamPaperModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param array $ids
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|ExamPaperModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return ExamPaperModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

    /**
     * @param array $ids
     * @return ResultsetInterface|Resultset|ExamPaperModel[]
     */
    public function findShallowExamPaperByIds($ids)
    {
        return ExamPaperModel::query()
            ->columns(['id', 'title', 'market_price', 'join_count'])
            ->inWhere('id', $ids)
            ->execute();
    }

    /**
     * @param int $paperId
     * @return ResultsetInterface|Resultset|ExamQuestionModel[]
     */
    public function findQuestions($paperId)
    {
        return $this->modelsManager->createBuilder()
            ->columns('q.*')
            ->addFrom(ExamQuestionModel::class, 'q')
            ->join(ExamPaperQuestionModel::class, 'q.id = pq.question_id', 'pq')
            ->where('pq.paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('pq.deleted = 0')
            ->orderBy('q.model ASC, pq.priority ASC')
            ->getQuery()->execute();
    }

    /**
     * @param int $paperId
     * @return ResultsetInterface|Resultset|ExamPaperUserModel[]
     */
    public function findExamPaperUsers($paperId)
    {
        $status = ExamPaperUserModel::STATUS_FINISHED;

        return ExamPaperUserModel::query()
            ->where('paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('status = :status:', ['status' => $status])
            ->andWhere('debut = 1')
            ->execute();
    }

    /**
     * @param int $paperId
     * @return ResultsetInterface|Resultset|ExamQuestionUserModel[]
     */
    public function findExamQuestionUsers($paperId)
    {
        $status = ExamPaperUserModel::STATUS_FINISHED;

        return $this->modelsManager->createBuilder()
            ->columns('b.*')
            ->addFrom(ExamPaperUserModel::class, 'a')
            ->join(ExamQuestionUserModel::class, 'a.id = b.paper_user_id', 'b')
            ->where('a.paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('a.status = :status:', ['status' => $status])
            ->andWhere('a.debut = 1')
            ->getQuery()->execute();
    }

    public function countExamPapers()
    {
        return (int)ExamPaperModel::count([
            'conditions' => 'published = 1 AND deleted = 0',
        ]);
    }

    public function countJoins($paperId)
    {
        return (int)ExamPaperUserModel::count([
            'conditions' => 'paper_id = ?1 AND debut = 1 AND deleted = 0',
            'bind' => [1 => $paperId],
        ]);
    }

    protected function getTaggedPaperIds($tagId)
    {
        $tagIds = is_array($tagId) ? $tagId : [$tagId];

        $repo = new ExamPaperTag();

        $rows = $repo->findByTagIds($tagIds);

        $result = [];

        if ($rows->count() > 0) {
            $result = kg_array_column($rows->toArray(), 'paper_id');
        }

        return $result;
    }

}
