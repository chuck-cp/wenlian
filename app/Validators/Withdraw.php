<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Withdraw as WithdrawModel;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Repos\Account as AccountRepo;
use App\Repos\User as UserRepo;
use App\Repos\Withdraw as WithdrawRepo;

class Withdraw extends Validator
{

    public function checkById($id)
    {
        $withdrawRepo = new WithdrawRepo();

        $withdraw = $withdrawRepo->findById($id);

        if (!$withdraw) {
            throw new BadRequestException('withdraw.not_found');
        }

        return $withdraw;
    }

    public function checkBySn($sn)
    {
        $withdrawRepo = new WithdrawRepo();

        $withdraw = $withdrawRepo->findBySn($sn);

        if (!$withdraw) {
            throw new BadRequestException('withdraw.not_found');
        }

        return $withdraw;
    }

    public function checkAccount($accountId)
    {
        $validator = new WithdrawAccount();

        return $validator->checkWithdrawAccount($accountId);
    }

    public function checkAmount($userId, $amount)
    {
        $amount = $this->filter->sanitize($amount, ['trim', 'float']);

        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($userId);

        $settings = $this->getSettings('withdraw');

        if ($amount < 0) {
            throw new BadRequestException('withdraw.invalid_amount');
        }

        if ($amount > $balance->cash) {
            throw new BadRequestException('withdraw.balance_not_enough');
        }

        if ($amount < $settings['min_amount'] || $amount > $settings['max_amount']) {
            throw new BadRequestException('withdraw.invalid_amount_range');
        }

        return $amount;
    }

    public function checkLoginPassword($userId, $password)
    {
        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($userId);

        $validator = new Account();

        $validator->checkLoginPassword($account, $password);
    }

    public function checkReviewStatus($status)
    {
        $list = [
            WithdrawModel::STATUS_APPROVED,
            WithdrawModel::STATUS_REFUSED,
        ];

        if (!in_array($status, $list)) {
            throw new BadRequestException('withdraw.invalid_status');
        }

        return $status;
    }

    public function checkReviewNote($note)
    {
        $value = $this->filter->sanitize($note, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2) {
            throw new BadRequestException('withdraw.review_note_too_short');
        }

        if ($length > 255) {
            throw new BadRequestException('withdraw.review_note_too_long');
        }

        return $value;
    }

    public function checkIfAllowApply($userId, $accountId)
    {
        $account = $this->checkAccount($accountId);

        $channels = WithdrawAccountModel::getEnabledChannels();

        if (!array_key_exists($account->channel, $channels)) {
            throw new BadRequestException('withdraw.channel_disabled');
        }

        $withdrawRepo = new WithdrawRepo();

        $withdraw = $withdrawRepo->findUserLastWithdraw($userId);

        $scopes = [
            WithdrawModel::STATUS_PENDING,
            WithdrawModel::STATUS_APPROVED,
        ];

        if ($withdraw && in_array($withdraw->status, $scopes)) {
            throw new BadRequestException('withdraw.has_applied');
        }

        $settings = $this->getSettings('withdraw');

        $monthlyCount = $withdrawRepo->countUserMonthlyWithdraws($userId);

        if ($monthlyCount >= $settings['monthly_limit']) {
            throw new BadRequestException('withdraw.reach_monthly_limit');
        }
    }

    public function checkIfAllowCancel(WithdrawModel $withdraw)
    {
        if ($withdraw->status != WithdrawModel::STATUS_PENDING) {
            throw new BadRequestException('withdraw.cancel_not_allowed');
        }
    }

    public function checkIfAllowReview(WithdrawModel $withdraw)
    {
        if ($withdraw->status != WithdrawModel::STATUS_PENDING) {
            throw new BadRequestException('withdraw.review_not_allowed');
        }
    }

}
