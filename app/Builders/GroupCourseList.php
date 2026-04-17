<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\Course as CourseRepo;
use App\Repos\Group as GroupRepo;

class GroupCourseList extends Builder
{

    public function handleGroups($relations)
    {
        $groups = $this->getGroups($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['group'] = $groups[$value['group_id']] ?? null;
        }

        return $relations;
    }

    public function handleCourses($relations)
    {
        $courses = $this->getCourses($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['course'] = $courses[$value['course_id']] ?? null;
        }

        return $relations;
    }

    public function getGroups($relations)
    {
        $ids = kg_array_column($relations, 'group_id');

        $groupRepo = new GroupRepo();

        $groups = $groupRepo->findShallowGroupByIds($ids);

        $result = [];

        foreach ($groups->toArray() as $group) {
            $result[$group['id']] = $group;
        }

        return $result;
    }

    public function getCourses($relations)
    {
        $ids = kg_array_column($relations, 'course_id');

        $courseRepo = new CourseRepo();

        $courses = $courseRepo->findShallowCourseByIds($ids);

        $result = [];

        foreach ($courses->toArray() as $course) {
            $result[$course['id']] = $course;
        }

        return $result;
    }

}
