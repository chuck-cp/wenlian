<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;

class WithdrawAccount extends Validator
{

    public function checkWithdrawAccount($id)
    {
        $accountRepo = new WithdrawAccountRepo();

        $account = $accountRepo->findById($id);

        if (!$account) {
            throw new BadRequestException('withdraw_account.not_found');
        }

        return $account;
    }

    public function checkName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        if (!CommonValidator::nickname($name)) {
            throw new BadRequestException('withdraw_account.invalid_name');
        }

        return $value;
    }

    public function checkChannel($channel)
    {
        if (!array_key_exists($channel, WithdrawAccountModel::channelTypes())) {
            throw new BadRequestException('withdraw_account.invalid_channel');
        }

        return $channel;
    }

    public function checkAccount($account)
    {
        $value = $this->filter->sanitize($account, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 30) {
            throw new BadRequestException('withdraw_account.invalid_account');
        }

        return $value;
    }

}
