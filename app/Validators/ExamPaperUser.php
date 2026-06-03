<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Services\Logic\Exam\Pilot as PilotService;

class ExamPaperUser extends Validator
{

    public function checkById($id)
    {
        $repo = new ExamPaperUserRepo();

        $paperUser = $repo->findById($id);

        if (!$paperUser) {
            throw new BadRequestException('exam_paper_user.not_found');
        }

        return $paperUser;
    }

    public function checkExamPaper($id)
    {
        $validator = new ExamPaper();

        return $validator->checkExamPaper($id);
    }

    public function checkUser($name)
    {
        $validator = new Account();

        $account = $validator->checkAccount($name);

        $validator = new User();

        return $validator->checkUser($account->id);
    }

    public function checkExpiryTime($expiryTime)
    {
        $value = $this->filter->sanitize($expiryTime, ['trim', 'string']);

        if (!CommonValidator::date($value, 'Y-m-d H:i:s')) {
            throw new BadRequestException('exam_paper_user.invalid_expiry_time');
        }

        return strtotime($value);
    }

    public function checkIfActiveMock(ExamPaperUserModel $paperUser)
    {
        if ($paperUser->start_time + $paperUser->paper_duration < time()) {
            throw new BadRequestException('exam_paper_user.exam_over');
        }

        if ($paperUser->start_time == 0) {
            throw new BadRequestException('exam_paper_user.exam_not_started');
        }

        if ($paperUser->end_time > 0) {
            throw new BadRequestException('exam_paper_user.paper_submitted');
        }
    }

    public function checkIfManualGrade($type)
    {
        $list = [
            ExamPaperModel::GRADE_TYPE_TEACHER,
            ExamPaperModel::GRADE_TYPE_STUDENT,
        ];

        if (!in_array($type, $list)) {
            throw new BadRequestException('exam_paper_user.grade_not_allowed');
        }
    }

    public function checkAuthCode($paperUserId, $authCode)
    {
        $service = new PilotService();

        if (!$service->checkAuthCode($paperUserId, $authCode)) {
            throw new BadRequestException('exam_paper_user.invalid_auth_code');
        }
    }

}
