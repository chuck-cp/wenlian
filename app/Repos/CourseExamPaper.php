<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Models\CourseExamPaper as CourseExamPaperModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CourseExamPaper extends Repository
{

    /**
     * @param int $courseId
     * @param int $paperId
     * @return CourseExamPaperModel|Model|bool
     */
    public function findCourseExamPaper($courseId, $paperId)
    {
        return CourseExamPaperModel::findFirst([
            'conditions' => 'course_id = :course_id: AND paper_id = :paper_id:',
            'bind' => ['course_id' => $courseId, 'paper_id' => $paperId],
        ]);
    }

    /**
     * @param array $courseIds
     * @return ResultsetInterface|Resultset|CourseExamPaperModel[]
     */
    public function findByCourseIds($courseIds)
    {
        return CourseExamPaperModel::query()
            ->inWhere('course_id', $courseIds)
            ->execute();
    }

}
