<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class MockPaperSubmit extends LogicService
{

    use AnswerSubmitTrait;
    use PaperSubmitTrait;

    public function handle($id)
    {
        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        $user = $this->getLoginUser();

        $validator->checkOwner($user->id, $paperUser->user_id);

        return $this->handlePaperSubmit($paperUser);
    }

    public function handlePaperSubmit(ExamPaperUserModel $paperUser)
    {
        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($paperUser->paper_id);

        if ($paper->exam_type != ExamPaperModel::EXAM_TYPE_MOCK) {
            return $paperUser;
        }

        $questionUserRepo = new ExamQuestionUserRepo();

        $questionUsers = $questionUserRepo->findByPaperUserId($paperUser->id);

        $containSubjectiveQuestions = $this->containSubjectiveQuestions($questionUsers);

        $userScore = $this->sumPaperUserScore($paperUser->id);

        $paperUser->user_score = $userScore;

        $paperUser->end_time = time();

        $userDuration = $paperUser->end_time - $paperUser->start_time;

        if ($userDuration > $paperUser->paper_duration) {
            $userDuration = $paperUser->paper_duration;
        }

        $paperUser->user_duration = $userDuration;

        $status = ExamPaperUserModel::STATUS_FINISHED;

        $isManualGrade = in_array($paper->grade_type, [
            ExamPaperModel::GRADE_TYPE_TEACHER,
            ExamPaperModel::GRADE_TYPE_STUDENT,
        ]);

        /**
         * 包含主观题 +人工判卷，进入待阅卷状态
         */
        if ($containSubjectiveQuestions && $isManualGrade) {
            $status = ExamPaperUserModel::STATUS_WAITING;
        }

        $paperUser->status = $status;

        $gradeType = $paper->grade_type;

        /**
         * 非首次考试 + 包含主观题 + 人工判卷，由学员自己阅卷
         */
        if ($containSubjectiveQuestions && $isManualGrade && $paperUser->debut == 0) {
            $gradeType = ExamPaperModel::GRADE_TYPE_STUDENT;
        }

        $paperUser->grade_type = $gradeType;

        try {

            $this->db->begin();

            $paperUser->update();

            // 满足（首次考试 + 不含主观题），才纳入试卷通过率统计
            if ($paperUser->debut == 1 && !$containSubjectiveQuestions) {
                $this->handlePaperPassCount($paperUser);
            }

            foreach ($questionUsers as $questionUser) {
                $this->handleQuestionPassCount($questionUser);
                $this->handleQuestionMistake($questionUser);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Submit Mock Paper Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }

        return $paperUser;
    }

    /**
     * 是否包含主观题
     *
     * @param ExamQuestionUserModel[] $questionUsers
     * @return bool
     */
    protected function containSubjectiveQuestions($questionUsers)
    {
        $models = [ExamQuestionModel::MODEL_SHORT_ANSWER];

        foreach ($questionUsers as $questionUser) {
            if (in_array($questionUser->question_model, $models)) {
                return true;
            }
        }

        return false;
    }

}
