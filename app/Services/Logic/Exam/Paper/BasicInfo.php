<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\ExamPaper as ExamPaperModel;
use App\Services\Category as CategoryService;
use App\Services\Logic\ContentTrait;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo as ShallowUserInfoService;

class BasicInfo extends LogicService
{

    use ExamPaperTrait;
    use ContentTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaper($id);

        return $this->handleBasicInfo($paper);
    }

    public function handleBasicInfo(ExamPaperModel $paper)
    {
        $categoryPaths = $this->handleCategoryPaths($paper->category_id);
        $teacher = $this->handleTeacherInfo($paper->teacher_id);
        $details = $this->handleContent($paper->details);
        $settings = $this->handleExamSettings();

        return [
            'id' => $paper->id,
            'title' => $paper->title,
            'cover' => $paper->cover,
            'summary' => $paper->summary,
            'keywords' => $paper->keywords,
            'teacher' => $teacher,
            'details' => $details,
            'tags' => $paper->tags,
            'level' => $paper->level,
            'duration' => $paper->duration,
            'attrs' => $paper->attrs,
            'settings' => $settings,
            'exam_type' => $paper->exam_type,
            'pack_type' => $paper->pack_type,
            'grade_type' => $paper->grade_type,
            'total_score' => $paper->total_score,
            'pass_score' => $paper->pass_score,
            'market_price' => (float)$paper->market_price,
            'vip_price' => (float)$paper->vip_price,
            'study_expiry' => $paper->study_expiry,
            'refund_expiry' => $paper->refund_expiry,
            'category_paths' => $categoryPaths,
            'featured' => $paper->featured,
            'published' => $paper->published,
            'deleted' => $paper->deleted,
            'question_count' => $paper->question_count,
            'favorite_count' => $paper->favorite_count,
            'join_count' => $paper->getJoinCount(),
            'pass_count' => $paper->pass_count,
            'create_time' => $paper->create_time,
            'update_time' => $paper->update_time,
        ];
    }

    protected function handleExamSettings()
    {
        $settings = kg_setting('exam');

        return [
            'switch_anti_enabled' => $settings['switch_anti_enabled'],
        ];
    }

    protected function handleCategoryPaths($categoryId)
    {
        if ($categoryId == 0) return [];

        $service = new CategoryService();

        return $service->getCategoryPaths($categoryId);
    }

    protected function handleTeacherInfo($userId)
    {
        if ($userId == 0) return new \stdClass();

        $service = new ShallowUserInfoService();

        return $service->handle($userId);
    }

}
