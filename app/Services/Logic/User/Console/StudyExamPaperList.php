<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\StudyExamPaperList as UserStudyPaperListService;

class StudyExamPaperList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $service = new UserStudyPaperListService();

        return $service->handle($user->id);
    }

}
