<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Withdraw;

use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo;
use App\Validators\WithdrawAccount as WithdrawAccountValidator;

class AccountInfo extends LogicService
{

    public function handle($id)
    {
        $validator = new WithdrawAccountValidator();

        $account = $validator->checkWithdrawAccount($id);

        return $this->handleAccount($account);
    }

    protected function handleAccount(WithdrawAccountModel $account)
    {
        $user = $this->handleUserInfo($account->user_id);

        return [
            'id' => $account->id,
            'name' => $account->name,
            'channel' => $account->channel,
            'account' => $account->account,
            'master' => $account->master,
            'verified' => $account->verified,
            'deleted' => $account->deleted,
            'create_time' => $account->create_time,
            'update_time' => $account->update_time,
            'user' => $user,
        ];
    }

    protected function handleUserInfo($userId)
    {
        $service = new ShallowUserInfo();

        return $service->handle($userId);
    }

}
