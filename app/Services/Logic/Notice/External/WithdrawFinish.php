<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\Task as TaskModel;
use App\Models\Withdraw as WithdrawModel;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Repos\User as UserRepo;
use App\Repos\Withdraw as WithdrawRepo;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Services\Logic\Notice\External\Sms\WithdrawFinish as SmsWithdrawFinishNotice;
use App\Services\Logic\Notice\External\WeChat\WithdrawFinish as WeChatWithdrawFinishNotice;
use App\Services\Logic\Service as LogicService;

class WithdrawFinish extends LogicService
{

    public function handleTask(TaskModel $task)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        $withdrawId = $task->item_id;

        $withdrawRepo = new WithdrawRepo();

        $withdraw = $withdrawRepo->findById($withdrawId);

        $withdrawAccountRepo = new WithdrawAccountRepo();

        $withdrawAccount = $withdrawAccountRepo->findById($withdraw->account_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($withdraw->user_id);

        $params = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'withdraw' => [
                'id' => $withdraw->id,
                'channel' => $this->getChannelText($withdrawAccount->channel),
                'apply_amount' => $withdraw->apply_amount,
                'trans_amount' => $withdraw->trans_amount,
                'service_fee' => $withdraw->service_fee,
                'create_time' => $withdraw->create_time,
            ],
        ];

        if ($wechatNoticeEnabled) {
            $notice = new WeChatWithdrawFinishNotice();
            $notice->handle($params);
        }

        if ($smsNoticeEnabled) {
            $notice = new SmsWithdrawFinishNotice();
            $notice->handle($params);
        }
    }

    public function createTask(WithdrawModel $withdraw)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $withdraw->id;
        $task->item_type = TaskModel::TYPE_NOTICE_WITHDRAW_FINISH;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    public function wechatNoticeEnabled()
    {
        $oa = $this->getSettings('wechat.oa');

        if ($oa['enabled'] == 0) return false;

        $template = json_decode($oa['notice_template'], true);

        $result = $template['withdraw_finish']['enabled'] ?? 0;

        return $result == 1;
    }

    public function smsNoticeEnabled()
    {
        $sms = $this->getSettings('sms');

        $template = json_decode($sms['template'], true);

        $result = $template['withdraw_finish']['enabled'] ?? 0;

        return $result == 1;
    }

    protected function getChannelText($channel)
    {
        $types = WithdrawAccountModel::channelTypes();

        return $types[$channel] ?? 'N/A';
    }

}
