<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Models\UserBalance as UserBalanceModel;
use App\Repos\User as UserRepo;
use App\Services\Logic\Service as LogicService;

class BalanceInfo extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser(true);

        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($user->id);

        if (!$balance) {
            $balance = new UserBalanceModel();
        }

        return [
            'cash' => (float)$balance->cash,
            'invoice' => (float)$balance->invoice,
            'point' => $balance->point,
        ];
    }

}
