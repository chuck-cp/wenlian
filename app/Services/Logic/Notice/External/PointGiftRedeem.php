<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\KgProduct as KgProductModel;
use App\Models\PointGiftRedeem as PointGiftRedeemModel;
use App\Models\Task as TaskModel;
use App\Repos\Account as AccountRepo;
use App\Repos\PointGiftRedeem as PointGiftRedeemRepo;
use App\Services\Logic\Notice\External\DingTalk\PointGiftRedeem as DingTalkPointGiftRedeemNotice;
use App\Services\Logic\Notice\External\WeWork\PointGiftRedeem as WeWorkPointGiftRedeemNotice;
use App\Services\Logic\Service as LogicService;

class PointGiftRedeem extends LogicService
{

    use RobotTrait;

    public function handleTask(TaskModel $task)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        $redeemRepo = new PointGiftRedeemRepo();

        $redeem = $redeemRepo->findById($task->item_id);

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($redeem->user_id);

        $params = [
            'account' => [
                'email' => $account->email,
                'phone' => $account->phone,
            ],
            'user' => [
                'id' => $redeem->user_id,
                'name' => $redeem->user_name,
            ],
            'gift' => [
                'id' => $redeem->gift_id,
                'name' => $redeem->gift_name,
            ],
        ];

        if ($weworkNoticeEnabled) {
            $notice = new WeWorkPointGiftRedeemNotice();
            $notice->handle($params);
        }

        if ($dingtalkNoticeEnabled) {
            $notice = new DingTalkPointGiftRedeemNotice();
            $notice->handle($params);
        }
    }

    public function createTask(PointGiftRedeemModel $redeem)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        if (!$weworkNoticeEnabled && !$dingtalkNoticeEnabled) return;

        if ($redeem->gift_type != KgProductModel::ITEM_GOODS) return;

        $task = new TaskModel();

        $task->item_id = $redeem->id;
        $task->item_type = TaskModel::TYPE_STAFF_NOTICE_POINT_GIFT_REDEEM;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

}
