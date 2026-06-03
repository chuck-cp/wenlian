<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class UnitReset extends LogicService
{

    public function handle($id)
    {
        $user = $this->getLoginUser(true);

        $paperUserValidator = new ExamPaperUserValidator();

        $paperUser = $paperUserValidator->checkById($id);

        $paperUserValidator->checkOwner($user->id, $paperUser->user_id);

        $questionUserRepo = new ExamQuestionUserRepo();

        $questionUserRepo->undoFinishedByPaperUserId($paperUser->id);
    }

}
