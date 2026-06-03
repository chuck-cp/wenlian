<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Models\Withdraw as WithdrawModel;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;

class WithdrawList extends Builder
{

    public function handleAccounts(array $withdraws)
    {
        $accounts = $this->getAccounts($withdraws);

        foreach ($withdraws as $key => $withdraw) {
            $withdraws[$key]['account'] = $accounts[$withdraw['account_id']] ?? null;
        }

        return $withdraws;
    }

    public function handleUsers(array $withdraws)
    {
        $users = $this->getUsers($withdraws);

        foreach ($withdraws as $key => $withdraw) {
            $withdraws[$key]['user'] = $users[$withdraw['user_id']] ?? null;
        }

        return $withdraws;
    }

    public function handleMeInfo(array $withdraw)
    {
        $me = [
            'allow_cancel' => 0,
        ];

        $scopes = [
            WithdrawModel::STATUS_PENDING,
            WithdrawModel::STATUS_APPROVED,
        ];

        if (in_array($withdraw['status'], $scopes)) {
            $me['allow_cancel'] = 1;
        }

        return $me;
    }

    public function getAccounts(array $withdraws)
    {
        $ids = kg_array_column($withdraws, 'account_id');

        $accountRepo = new WithdrawAccountRepo();

        $accounts = $accountRepo->findByIds($ids, ['id', 'name', 'channel', 'account']);

        $result = [];

        foreach ($accounts->toArray() as $account) {
            $result[$account['id']] = $account;
        }

        return $result;
    }

    public function getUsers(array $withdraws)
    {
        $ids = kg_array_column($withdraws, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
