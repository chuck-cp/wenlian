<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\GroupCourse as GroupCourseModel;
use Phalcon\Mvc\Model;

class GroupCourse extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(GroupCourseModel::class);

        $builder->where('1 = 1');

        if (!empty($where['group_id'])) {
            $builder->andWhere('group_id = :group_id:', ['group_id' => $where['group_id']]);
        }

        if (!empty($where['course_id'])) {
            $builder->andWhere('course_id = :course_id:', ['course_id' => $where['course_id']]);
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
     * @return GroupCourseModel|Model|bool
     */
    public function findById($id)
    {
        return GroupCourseModel::findFirst($id);
    }

    /**
     * @param int $groupId
     * @param int $courseId
     * @return GroupCourseModel|Model|bool
     */
    public function findGroupCourse($groupId, $courseId)
    {
        return GroupCourseModel::findFirst([
            'conditions' => 'group_id = ?1 AND course_id = ?2',
            'bind' => [1 => $groupId, 2 => $courseId],
            'order' => 'id DESC',
        ]);
    }

}
