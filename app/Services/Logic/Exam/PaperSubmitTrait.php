<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;

trait PaperSubmitTrait
{

    protected function sumPaperUserScore($paperUserId)
    {
        $paperUserRepo = new ExamPaperUserRepo();

        $paperUser = $paperUserRepo->findById($paperUserId);

        $questionUserRepo = new ExamQuestionUserRepo();

        $records = $questionUserRepo->findByPaperUserId($paperUser->id);

        if ($records->count() == 0) return 0;

        $userScore = 0;

        foreach ($records as $record) {
            if ($record->question_model != ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
                $userScore += $record->user_score;
            }
        }

        return $userScore;
    }

    protected function handlePaperPassCount(ExamPaperUserModel $paperUser)
    {
        if ($paperUser->debut == 0) return;

        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($paperUser->paper_id);

        if ($paperUser->user_score >= $paper->total_score * 0.6) {
            $paperUser->passed = 1;
            $paperUser->update();

            $paper->pass_count += 1;
            $paper->update();
        }
    }

}