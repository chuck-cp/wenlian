<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class PaperUser extends LogicService
{

    public function handle($id)
    {
        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        return $this->handlePaperUser($paperUser);
    }

    public function handlePaperUser(ExamPaperUserModel $paperUser)
    {
        return [
            'id' => $paperUser->id,
            'paper_id' => $paperUser->paper_id,
            'user_id' => $paperUser->user_id,
            'source_type' => $paperUser->source_type,
            'grade_type' => $paperUser->grade_type,
            'expiry_time' => $paperUser->expiry_time,
            'paper_duration' => $paperUser->paper_duration,
            'user_duration' => $paperUser->user_duration,
            'paper_score' => $paperUser->paper_score,
            'pass_score' => $paperUser->pass_score,
            'user_score' => $paperUser->user_score,
            'start_time' => $paperUser->start_time,
            'end_time' => $paperUser->end_time,
            'debut' => $paperUser->debut,
            'status' => $paperUser->status,
            'passed' => $paperUser->passed,
        ];
    }

}
