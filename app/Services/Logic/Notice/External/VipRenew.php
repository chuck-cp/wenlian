<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\Task as TaskModel;
use App\Models\User as UserModel;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\Sms\VipRenew as SmsVipRenewNotice;
use App\Services\Logic\Notice\External\WeChat\VipRenew as WeChatVipRenewNotice;
use App\Services\Logic\Service as LogicService;

class VipRenew extends LogicService
{

    public function handleTask(TaskModel $task)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) {
            return null;
        }

        $userId = $task->item_id;

        $userRepo = new UserRepo();

        $user = $userRepo->findById($userId);

        $params = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'vip_expiry_time' => $user->vip_expiry_time,
            ],
        ];

        if ($wechatNoticeEnabled) {
            $notice = new WeChatVipRenewNotice();
            $notice->handle($params);
        }

        if ($smsNoticeEnabled) {
            $notice = new SmsVipRenewNotice();
            $notice->handle($params);
        }
    }

    public function createTask(UserModel $user)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $user->id;
        $task->item_type = TaskModel::TYPE_NOTICE_VIP_EXPIRY;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    public function wechatNoticeEnabled()
    {
        $oa = $this->getSettings('wechat.oa');

        if ($oa['enabled'] == 0) return false;

        $template = json_decode($oa['notice_template'], true);

        $result = $template['vip_renew']['enabled'] ?? 0;

        return $result == 1;
    }

    public function smsNoticeEnabled()
    {
        $sms = $this->getSettings('sms');

        $template = json_decode($sms['template'], true);

        $result = $template['vip_renew']['enabled'] ?? 0;

        return $result == 1;
    }

}
