<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\Consult as ConsultModel;
use App\Models\Task as TaskModel;
use App\Repos\Account as AccountRepo;
use App\Repos\Consult as ConsultRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\DingTalk\ConsultCreate as DingTalkConsultCreateNotice;
use App\Services\Logic\Notice\External\WeWork\ConsultCreate as WeWorkConsultCreateNotice;
use App\Services\Logic\Service as LogicService;

class ConsultCreate extends LogicService
{

    use RobotTrait;

    public function handleTask(TaskModel $task)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        $consultRepo = new ConsultRepo();

        $consult = $consultRepo->findById($task->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($consult->owner_id);

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($consult->owner_id);

        $courseRepo = new CourseRepo();

        $course = $courseRepo->findById($consult->course_id);

        $params = [
            'account' => [
                'email' => $account->email,
                'phone' => $account->phone,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
            'consult' => [
                'id' => $consult->id,
                'question' => $consult->question,
            ],
        ];

        if ($weworkNoticeEnabled) {
            $notice = new WeWorkConsultCreateNotice();
            $notice->handle($params);
        }

        if ($dingtalkNoticeEnabled) {
            $notice = new DingTalkConsultCreateNotice();
            $notice->handle($params);
        }
    }

    public function createTask(ConsultModel $consult)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        if (!$weworkNoticeEnabled && !$dingtalkNoticeEnabled) return;

        $keyName = "consult_create_notice:{$consult->owner_id}";

        $cache = $this->getCache();

        $content = $cache->get($keyName);

        if ($content) return;

        $cache->save($keyName, 1, 3600);

        $task = new TaskModel();

        $task->item_id = $consult->id;
        $task->item_type = TaskModel::TYPE_STAFF_NOTICE_CONSULT_CREATE;
        $task->priority = TaskModel::PRIORITY_LOW;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

}
