<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\Task as TaskModel;
use App\Services\Logic\Notice\External\DingTalk\ServerMonitor as DingTalkServerMonitorNotice;
use App\Services\Logic\Notice\External\WeWork\ServerMonitor as WeWorkServerMonitorNotice;
use App\Services\Logic\Service as LogicService;

class ServerMonitor extends LogicService
{

    use RobotTrait;

    public function handleTask(TaskModel $task)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        $params = [
            'content' => $task->item_info['content'],
        ];

        if ($weworkNoticeEnabled) {
            $notice = new WeWorkServerMonitorNotice();
            $notice->handle($params);
        }

        if ($dingtalkNoticeEnabled) {
            $notice = new DingTalkServerMonitorNotice();
            $notice->handle($params);
        }
    }

    public function createTask($content)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        if (!$weworkNoticeEnabled && !$dingtalkNoticeEnabled) return;

        $task = new TaskModel();

        $itemInfo = ['content' => $content];

        $task->item_id = time();
        $task->item_info = $itemInfo;
        $task->item_type = TaskModel::TYPE_STAFF_NOTICE_SERVER_MONITOR;
        $task->priority = TaskModel::PRIORITY_HIGH;
        $task->status = TaskModel::STATUS_PENDING;
        $task->max_try_count = 1;

        $task->create();
    }

}