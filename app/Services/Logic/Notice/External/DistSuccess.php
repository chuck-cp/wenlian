<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\CashHistory as CashHistoryModel;
use App\Models\Task as TaskModel;
use App\Repos\CashHistory as CashHistoryRepo;
use App\Repos\Order as OrderRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\Sms\DistSuccess as SmsDistSuccessNotice;
use App\Services\Logic\Notice\External\WeChat\DistSuccess as WeChatDistSuccessNotice;
use App\Services\Logic\Service as LogicService;

class DistSuccess extends LogicService
{

    public function handleTask(TaskModel $task)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        $historyId = $task->item_id;

        $historyRepo = new CashHistoryRepo();

        $history = $historyRepo->findById($historyId);

        $orderRepo = new OrderRepo();

        $order = $orderRepo->findById($history->event_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($history->user_id);

        $params = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'order' => [
                'id' => $order->id,
                'subject' => $order->subject,
                'amount' => $order->amount,
            ],
            'reward' => [
                'amount' => $history->event_amount,
            ],
        ];

        if ($wechatNoticeEnabled) {
            $notice = new WeChatDistSuccessNotice();
            $notice->handle($params);
        }

        if ($smsNoticeEnabled) {
            $notice = new SmsDistSuccessNotice();
            $notice->handle($params);
        }
    }

    public function createTask(CashHistoryModel $history)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $history->id;
        $task->item_type = TaskModel::TYPE_NOTICE_DIST_SUCCESS;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    public function wechatNoticeEnabled()
    {
        $oa = $this->getSettings('wechat.oa');

        if ($oa['enabled'] == 0) return false;

        $template = json_decode($oa['notice_template'], true);

        $result = $template['dist_success']['enabled'] ?? 0;

        return $result == 1;
    }

    public function smsNoticeEnabled()
    {
        $sms = $this->getSettings('sms');

        $template = json_decode($sms['template'], true);

        $result = $template['dist_success']['enabled'] ?? 0;

        return $result == 1;
    }

}
