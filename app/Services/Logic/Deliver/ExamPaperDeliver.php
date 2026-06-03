<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Deliver;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Models\User as UserModel;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Services\Logic\Exam\Paper\PaperUserTrait;
use App\Services\Logic\Service as LogicService;

class ExamPaperDeliver extends LogicService
{

    use PaperUserTrait;

    public function handle(ExamPaperModel $paper, UserModel $user)
    {
        $this->revokeExamPaperUser($paper, $user);
        $this->handleExamPaperUser($paper, $user);
    }

    protected function handleExamPaperUser(ExamPaperModel $paper, UserModel $user)
    {
        $expiryTime = strtotime("+{$paper->study_expiry} months");

        $sourceType = KgOwnershipModel::SOURCE_CHARGE;

        $this->assignUserPaper($paper, $user, $expiryTime, $sourceType);
        $this->recountPaperJoins($paper);
        $this->recountUserStudyPapers($user);
    }

    protected function revokeExamPaperUser(ExamPaperModel $paper, UserModel $user)
    {
        $paperUserRepo = new ExamPaperUserRepo();

        $relations = $paperUserRepo->findByPaperAndUserId($paper->id, $user->id);

        if ($relations->count() == 0) return;

        foreach ($relations as $relation) {
            $this->deletePaperUser($relation);
        }
    }

}
