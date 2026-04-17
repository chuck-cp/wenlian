<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Group;

use App\Models\Article as ArticleModel;
use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\Group as GroupModel;
use App\Models\User as UserModel;
use App\Repos\GroupArticle as GroupArticleRepo;
use App\Repos\GroupCourse as GroupCourseRepo;
use App\Repos\GroupExamPaper as GroupExamPaperRepo;
use App\Repos\User as UserRepo;

trait GroupPermissionTrait
{

    protected function groupedCourse(CourseModel $course, UserModel $user)
    {
        $groups = $this->getUserGroups($user->id);

        if (!$groups) return false;

        $groupCourseRepo = new GroupCourseRepo();

        foreach ($groups as $group) {
            $groupCourse = $groupCourseRepo->findGroupCourse($group->id, $course->id);
            if ($groupCourse) {
                return true;
            }
        }

        return false;
    }

    protected function groupedExamPaper(ExamPaperModel $paper, UserModel $user)
    {
        $groups = $this->getUserGroups($user->id);

        if (!$groups) return false;

        $groupPaperRepo = new GroupExamPaperRepo();

        foreach ($groups as $group) {
            $groupPaper = $groupPaperRepo->findGroupExamPaper($group->id, $paper->id);
            if ($groupPaper) return true;
        }

        return false;
    }

    protected function groupedArticle(ArticleModel $article, UserModel $user)
    {
        $groups = $this->getUserGroups($user->id);

        if (!$groups) return false;

        $groupArticleRepo = new GroupArticleRepo();

        foreach ($groups as $group) {
            $groupArticle = $groupArticleRepo->findGroupArticle($group->id, $article->id);
            if ($groupArticle) return true;
        }

        return false;
    }

    /**
     * @param int $userId
     * @return array|GroupModel[]
     */
    protected function getUserGroups($userId)
    {
        $userRepo = new UserRepo();

        $groups = $userRepo->findGroups($userId);

        return $groups->filter(function ($group) {
            if ($group->deleted == 0 && $group->published == 1 && $group->expiry_time > time()) {
                return $group;
            }
            return false;
        });
    }

}
