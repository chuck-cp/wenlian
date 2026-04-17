<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\GroupExamPaper as GroupExamPaperRepo;

class GroupExamPaper extends Validator
{

    public function checkById($id)
    {
        $repo = new GroupExamPaperRepo();

        $groupCourse = $repo->findById($id);

        if (!$groupCourse) {
            throw new BadRequestException('group_exam_paper.not_found');
        }

        return $groupCourse;
    }

    public function checkGroup($id)
    {
        $validator = new Group();

        return $validator->checkGroup($id);
    }

    public function checkExamPaper($id)
    {
        $validator = new ExamPaper();

        return $validator->checkExamPaper($id);
    }

}
