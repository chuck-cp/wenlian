<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Services\Logic\Notice\External\PaperGradeFinish as PaperGradeFinishNotice;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class MockPaperGrade extends LogicService
{

    use PaperSubmitTrait;

    public function handle($id)
    {
        $authCode = $this->request->getPost('auth_code', ['trim', 'string']);

        $paperUserValidator = new ExamPaperUserValidator();

        $paperUser = $paperUserValidator->checkById($id);

        $paperUserValidator->checkIfManualGrade($paperUser->grade_type);

        if ($paperUser->grade_type == ExamPaperModel::GRADE_TYPE_TEACHER) {

            $paperUserValidator->checkAuthCode($paperUser->id, $authCode);

        } elseif ($paperUser->grade_type == ExamPaperModel::GRADE_TYPE_STUDENT) {

            $user = $this->getLoginUser();

            $paperUserValidator->checkOwner($user->id, $paperUser->user_id);
        }

        $userScore = $this->sumPaperUserScore($paperUser->id);

        $paperUser->user_score = $userScore;
        $paperUser->status = ExamPaperUserModel::STATUS_FINISHED;
        $paperUser->update();

        $this->handlePaperPassCount($paperUser);

        $notice = new PaperGradeFinishNotice();
        $notice->createTask($paperUser);

        return $paperUser;
    }

}
