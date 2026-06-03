<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Caches;

use App\Models\ExamPaper as ExamPaperModel;
use App\Repos\Course as CourseRepo;

class CourseExamPaperList extends Cache
{

    protected $lifetime = 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "course_exam_paper_list:{$id}";
    }

    public function getContent($id = null)
    {
        $courseRepo = new CourseRepo();

        $papers = $courseRepo->findExamPapers($id);

        if ($papers->count() == 0) {
            return [];
        }

        return $this->handleContent($papers);
    }

    /**
     * @param ExamPaperModel[] $papers
     * @return array
     */
    public function handleContent($papers)
    {
        $result = [];

        foreach ($papers as $paper) {
            $result[] = [
                'id' => $paper->id,
                'title' => $paper->title,
                'cover' => $paper->cover,
                'level' => $paper->level,
                'duration' => $paper->duration,
                'exam_type' => $paper->exam_type,
                'pack_type' => $paper->pack_type,
                'market_price' => (float)$paper->market_price,
                'vip_price' => (float)$paper->vip_price,
                'favorite_count' => $paper->favorite_count,
                'question_count' => $paper->question_count,
                'join_count' => $paper->join_count,
                'pass_count' => $paper->pass_count,
            ];
        }

        return $result;
    }

}
