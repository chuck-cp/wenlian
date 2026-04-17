<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Course as CourseModel;
use App\Models\CourseTopic as CourseTopicModel;
use App\Models\Topic as TopicModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Topic extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(TopicModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['title'])) {
            $builder->andWhere('title LIKE :title:', ['title' => "%{$where['title']}%"]);
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (isset($where['published'])) {
            $builder->andWhere('published = :published:', ['published' => $where['published']]);
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
     * @return TopicModel|Model|bool
     */
    public function findById($id)
    {
        return TopicModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param array $ids
     * @param array|string $columns
     * @return ResultsetInterface|Resultset|TopicModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return TopicModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

    /**
     * @param int $topicId
     * @return ResultsetInterface|Resultset|CourseModel[]
     */
    public function findCourses($topicId)
    {
        return $this->modelsManager->createBuilder()
            ->columns('c.*')
            ->addFrom(CourseModel::class, 'c')
            ->join(CourseTopicModel::class, 'c.id = ct.course_id', 'ct')
            ->where('ct.topic_id = :topic_id:', ['topic_id' => $topicId])
            ->andWhere('c.published = 1')
            ->andWhere('c.deleted = 0')
            ->getQuery()->execute();
    }

    /**
     * 后台证书编辑：可选专题列表（未删除）
     *
     * @return ResultsetInterface|Resultset|TopicModel[]
     */
    public function findForCertXm()
    {
        return TopicModel::find([
            'conditions' => 'deleted = :deleted:',
            'bind' => ['deleted' => 0],
            'order' => 'id DESC',
        ]);
    }

    /**
     * 首页：未删除的专题（与后台列表一致；是否发布仅影响详情页时由控制器处理）
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|TopicModel[]
     */
    public function findTopicsForHomeIndex($limit = 20)
    {
        return TopicModel::find([
            'conditions' => 'deleted = :deleted:',
            'bind' => ['deleted' => 0],
            'order' => 'id DESC',
            'limit' => (int)$limit,
        ]);
    }

    /**
     * 首页专题下课程 id 列表（已上架、已发布），按专题-课程关联记录倒序，最多 $limit 条
     *
     * @param int $topicId
     * @param int $limit
     * @return int[]
     */
    public function findCourseIdsForTopicHomeIndex($topicId, $limit = 8)
    {
        $rows = $this->modelsManager->createBuilder()
            ->columns(['c.id'])
            ->addFrom(CourseModel::class, 'c')
            ->join(CourseTopicModel::class, 'c.id = ct.course_id', 'ct')
            ->where('ct.topic_id = :topic_id:', ['topic_id' => $topicId])
            ->andWhere('c.published = 1')
            ->andWhere('c.deleted = 0')
            ->orderBy('ct.id DESC')
            ->limit((int)$limit)
            ->getQuery()
            ->execute();

        $ids = [];

        foreach ($rows as $row) {
            $ids[] = (int)$row->id;
        }

        return $ids;
    }

    public function countTopics()
    {
        return (int)TopicModel::count([
            'conditions' => 'published = 1 AND deleted = 0',
        ]);
    }

    public function countCourses($topicId)
    {
        return (int)CourseTopicModel::count([
            'conditions' => 'topic_id = :topic_id:',
            'bind' => ['topic_id' => $topicId],
        ]);
    }

}
