<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Models\User as UserModel;
use App\Repos\CourseExamPaper as CourseExamPaperRepo;
use App\Repos\CourseUser as CourseUserRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Group\GroupPermissionTrait;

trait PaperUserTrait
{

    use GroupPermissionTrait;

    /**
     * @var bool
     */
    protected $ownedPaper = false;

    /**
     * @var bool
     */
    protected $joinedPaper = false;

    /**
     * 首考资格
     *
     * @var ExamPaperUserModel|null
     */
    protected $debutPaperUser;

    /**
     * 当前资格
     *
     * @var ExamPaperUserModel|null
     */
    protected $currentPaperUser;

    protected function setPaperUser(ExamPaperModel $paper, UserModel $user)
    {
        if ($user->id == 0) return;

        $paperUserRepo = new ExamPaperUserRepo();

        $debutPaperUser = $paperUserRepo->findDebutPaperUser($paper->id, $user->id);

        $this->debutPaperUser = $debutPaperUser;

        if ($debutPaperUser) {
            $this->joinedPaper = true;
        }

        if ($paper->teacher_id == $user->id) {

            $this->ownedPaper = true;

        } elseif ($paper->market_price == 0) {

            $this->ownedPaper = true;

        } elseif ($paper->vip_price == 0 && $user->vip == 1) {

            $this->ownedPaper = true;

        } elseif ($this->groupedExamPaper($paper, $user)) {

            $this->ownedPaper = true;

        } elseif ($debutPaperUser) {

            $sourceTypes = [
                KgOwnershipModel::SOURCE_CHARGE,
                KgOwnershipModel::SOURCE_MANUAL,
                KgOwnershipModel::SOURCE_POINT_REDEEM,
                KgOwnershipModel::SOURCE_LUCKY_REDEEM,
            ];

            $case1 = $debutPaperUser->deleted == 0;
            $case2 = $debutPaperUser->expiry_time > time();
            $case3 = in_array($debutPaperUser->source_type, $sourceTypes);

            /**
             * 之前参与过试卷，但不再满足条件，视为未参与
             */
            if ($case1 && $case2 && $case3) {
                $this->ownedPaper = true;
            } else {
                $this->joinedPaper = false;
            }
        }

        /**
         * 查看参加过的课程是否绑定该试卷
         */
        if (!$this->ownedPaper) {

            $courseUserRepo = new CourseUserRepo();

            $relations = $courseUserRepo->findByUserId($user->id);

            if ($relations->count() == 0) return;

            $courseIds = [];

            /**
             * 过滤掉"试听"课程
             */
            foreach ($relations as $relation) {
                if ($relation->source_type != KgOwnershipModel::SOURCE_TRIAL) {
                    if ($relation->expiry_time > time()) {
                        $courseIds[] = $relation->course_id;
                    }
                }
            }

            if (count($courseIds) == 0) return;

            $coursePaperRepo = new CourseExamPaperRepo();

            $relations = $coursePaperRepo->findByCourseIds($courseIds);

            if ($relations->count() == 0) return;

            foreach ($relations as $relation) {
                if ($relation->paper_id == $paper->id) {
                    $this->ownedPaper = true;
                    break;
                }
            }
        }
    }

    protected function assignUserPaper(ExamPaperModel $paper, UserModel $user, int $expiryTime, int $sourceType)
    {
        if ($this->allowFreeAccess($paper, $user)) return null;

        $paperUserRepo = new ExamPaperUserRepo();

        $relation = $paperUserRepo->findDebutPaperUser($paper->id, $user->id);

        $newRelation = null;

        if (!$relation) {

            $newRelation = $this->createPaperUser($paper, $user, $expiryTime, $sourceType);

        } else {

            switch ($relation->source_type) {
                case KgOwnershipModel::SOURCE_FREE:
                case KgOwnershipModel::SOURCE_VIP:
                case KgOwnershipModel::SOURCE_TEACHER:
                case KgOwnershipModel::SOURCE_GROUP:
                    $newRelation = $this->createPaperUser($paper, $user, $expiryTime, $sourceType);
                    $this->deletePaperUser($relation);
                    break;
                case KgOwnershipModel::SOURCE_MANUAL:
                    $relation->expiry_time = $expiryTime;
                    $relation->update();
                    break;
                case KgOwnershipModel::SOURCE_CHARGE:
                case KgOwnershipModel::SOURCE_POINT_REDEEM:
                case KgOwnershipModel::SOURCE_LUCKY_REDEEM:
                    if ($relation->expiry_time < time()) {
                        $newRelation = $this->createPaperUser($paper, $user, $expiryTime, $sourceType);
                        $this->deletePaperUser($relation);
                    }
                    break;
            }
        }

        $this->recountPaperJoins($paper);
        $this->recountUserStudyPapers($user);

        return $newRelation ?: $relation;
    }

    protected function createPaperUser(ExamPaperModel $paper, UserModel $user, int $expiryTime, int $sourceType)
    {
        $paperUser = new ExamPaperUserModel();

        $paperUser->user_id = $user->id;
        $paperUser->paper_id = $paper->id;
        $paperUser->paper_score = $paper->total_score;
        $paperUser->paper_duration = 60 * $paper->duration;
        $paperUser->expiry_time = $expiryTime;
        $paperUser->source_type = $sourceType;
        $paperUser->status = ExamPaperUserModel::STATUS_PENDING;
        $paperUser->debut = 1;

        $paperUser->create();

        return $paperUser;
    }

    protected function deletePaperUser(ExamPaperUserModel $relation)
    {
        $relation->deleted = 1;

        $relation->update();
    }

    protected function recountPaperJoins(ExamPaperModel $paper)
    {
        $paperRepo = new ExamPaperRepo();

        $joinCount = $paperRepo->countJoins($paper->id);

        $paper->join_count = $joinCount;

        $paper->update();
    }

    protected function recountUserStudyPapers(UserModel $user)
    {
        $userRepo = new UserRepo();

        $studyPaperCount = $userRepo->countStudyPapers($user->id);

        $user->study_paper_count = $studyPaperCount;

        $user->update();
    }

    protected function allowFreeAccess(ExamPaperModel $paper, UserModel $user)
    {
        $result = false;

        if ($paper->market_price == 0) {
            $result = true;
        } elseif ($paper->vip_price == 0 && $user->vip == 1) {
            $result = true;
        } elseif ($paper->teacher_id == $user->id) {
            $result = true;
        } elseif ($this->groupedExamPaper($paper, $user)) {
            $result = true;
        }

        return $result;
    }

    protected function getFreeSourceType(ExamPaperModel $paper, UserModel $user)
    {
        if ($paper->teacher_id == $user->id) {
            return KgOwnershipModel::SOURCE_TEACHER;
        }

        $sourceType = KgOwnershipModel::SOURCE_FREE;

        if ($paper->market_price > 0) {
            if ($paper->vip_price == 0 && $user->vip == 1) {
                $sourceType = KgOwnershipModel::SOURCE_VIP;
            } elseif ($this->groupedExamPaper($paper, $user)) {
                $sourceType = KgOwnershipModel::SOURCE_GROUP;
            }
        }

        return $sourceType;
    }

}
