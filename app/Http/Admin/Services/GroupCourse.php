<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\GroupCourseList as GroupCourseListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Group as GroupModel;
use App\Models\GroupCourse as GroupCourseModel;
use App\Repos\Course as CourseRepo;
use App\Repos\Group as GroupRepo;
use App\Repos\GroupCourse as GroupCourseRepo;
use App\Validators\GroupCourse as GroupCourseValidator;

class GroupCourse extends Service
{

    public function create()
    {
        $post = $this->request->getPost();

        $validator = new GroupCourseValidator();

        $group = $validator->checkGroup($post['group_id']);

        $groupCourseRepo = new GroupCourseRepo();

        $courseIds = $post['xm_course_ids'] ? explode(',', $post['xm_course_ids']) : [];

        if (!$courseIds) return;

        foreach ($courseIds as $courseId) {

            $course = $validator->checkCourse($courseId);
            $groupCourse = $groupCourseRepo->findGroupCourse($group->id, $course->id);

            if (!$groupCourse) {
                $groupCourseModel = new GroupCourseModel();
                $groupCourseModel->group_id = $group->id;
                $groupCourseModel->course_id = $course->id;
                $groupCourseModel->create();
            }
        }

        $this->recountGroupCourses($group);
    }

    public function delete($id)
    {
        $validator = new GroupCourseValidator();

        $groupCourse = $validator->checkById($id);

        $group = $validator->checkGroup($groupCourse->group_id);

        $groupCourse->delete();

        $this->recountGroupCourses($group);
    }

    public function getCourses($id)
    {
        $validator = new GroupCourseValidator();

        $group = $validator->checkGroup($id);

        $pagerQuery = new PagerQuery();

        $params['group_id'] = $group->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GroupCourseRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleCourses($pager);
    }

    public function getXmCourses()
    {
        $courseRepo = new CourseRepo();

        $items = $courseRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => false,
            ];
        }

        return $result;
    }

    protected function recountGroupCourses(GroupModel $group)
    {
        $groupRepo = new GroupRepo();

        $courseCount = $groupRepo->countCourses($group->id);

        $group->course_count = $courseCount;

        $group->update();
    }

    protected function handleCourses($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new GroupCourseListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleCourses($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
