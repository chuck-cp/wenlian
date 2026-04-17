<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Repos\User as UserRepo;
use App\Repos\UserReferer as UserRefererRepo;
use App\Services\Logic\Service as LogicService;

class RefererInfo extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser(true);

        $repo = new UserRefererRepo();

        $record = $repo->findByUserParentLevel($user->id, 1);

        if (!$record) return new \stdClass();

        $userRepo = new UserRepo();

        $referUser = $userRepo->findShallowUserById($record->parent_id);

        return $referUser->toArray();
    }

}
