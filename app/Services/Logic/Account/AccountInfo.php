<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Account;

use App\Repos\Account as AccountRepo;
use App\Services\Logic\Service as LogicService;

class AccountInfo extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($user->id);

        return [
            'id' => $account->id,
            'email' => $account->email,
            'phone' => $account->phone,
            'password' => $account->password,
            'create_time' => $account->create_time,
            'update_time' => $account->update_time,
        ];
    }

}
