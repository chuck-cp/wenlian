<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Group as GroupModel;
use App\Models\GroupArticle as GroupArticleModel;
use App\Models\GroupCourse as GroupCourseModel;
use App\Models\GroupExamPaper as GroupExamPaperModel;
use App\Models\GroupUser as GroupUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Group extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(GroupModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['name'])) {
            $builder->andWhere('name LIKE :name:', ['name' => "%{$where['name']}%"]);
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
     * @param array $where
     * @param string $sort
     * @return ResultsetInterface|Resultset|GroupModel[]
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
     * @return GroupModel|Model|bool
     */
    public function findById($id)
    {
        return GroupModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param string $name
     * @return GroupModel|Model|bool
     */
    public function findByName($name)
    {
        return GroupModel::findFirst([
            'conditions' => 'name = :name:',
            'bind' => ['name' => $name],
        ]);
    }

    /**
     * @param array $ids
     * @return ResultsetInterface|Resultset|GroupModel[]
     */
    public function findShallowGroupByIds($ids)
    {
        return GroupModel::query()
            ->columns(['id', 'name'])
            ->inWhere('id', $ids)
            ->execute();
    }

    public function countUsers($groupId)
    {
        return (int)GroupUserModel::count([
            'conditions' => 'group_id = :group_id:',
            'bind' => ['group_id' => $groupId],
        ]);
    }

    public function countCourses($groupId)
    {
        return (int)GroupCourseModel::count([
            'conditions' => 'group_id = :group_id:',
            'bind' => ['group_id' => $groupId],
        ]);
    }

    public function countExamPapers($groupId)
    {
        return (int)GroupExamPaperModel::count([
            'conditions' => 'group_id = :group_id:',
            'bind' => ['group_id' => $groupId],
        ]);
    }

    public function countArticles($groupId)
    {
        return (int)GroupArticleModel::count([
            'conditions' => 'group_id = :group_id:',
            'bind' => ['group_id' => $groupId],
        ]);
    }

}
