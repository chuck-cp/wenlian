<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Course;

use App\Caches\CourseExamPaperList as CourseExamPaperListCache;
use App\Services\Logic\CourseTrait;
use App\Services\Logic\Service as LogicService;

class ExamPaperList extends LogicService
{

    use CourseTrait;

    public function handle($id)
    {
        $course = $this->checkCourseCache($id);

        $cache = new CourseExamPaperListCache();

        return $cache->get($course->id);
    }

}
