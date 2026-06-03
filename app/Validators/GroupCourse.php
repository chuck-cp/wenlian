<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\GroupCourse as GroupCourseRepo;

class GroupCourse extends Validator
{

    public function checkById($id)
    {
        $repo = new GroupCourseRepo();

        $groupCourse = $repo->findById($id);

        if (!$groupCourse) {
            throw new BadRequestException('group_course.not_found');
        }

        return $groupCourse;
    }

    public function checkGroup($id)
    {
        $validator = new Group();

        return $validator->checkGroup($id);
    }

    public function checkCourse($id)
    {
        $validator = new Course();

        return $validator->checkCourse($id);
    }

}
