<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\ChapterLive as ChapterLiveModel;
use App\Models\Task as TaskModel;
use App\Repos\ChapterLive as ChapterLiveRepo;
use App\Repos\Course as CourseRepo;
use App\Services\Logic\Notice\External\DingTalk\TeacherLive as DingTalkTeacherLiveNotice;
use App\Services\Logic\Notice\External\WeWork\TeacherLive as WeWorkTeacherLiveNotice;
use App\Services\Logic\Service as LogicService;

class TeacherLive extends LogicService
{

    use RobotTrait;

    public function handleTask(TaskModel $task)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        $liveRepo = new ChapterLiveRepo();

        $live = $liveRepo->findById($task->item_id);

        $courseRepo = new CourseRepo();

        $course = $courseRepo->findById($live->course_id);

        $params = [
            'live' => [
                'id' => $live->id,
                'start_time' => $live->start_time,
            ],
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ]
        ];

        if ($weworkNoticeEnabled) {
            $notice = new WeWorkTeacherLiveNotice();
            $notice->handle($params);
        }

        if ($dingtalkNoticeEnabled) {
            $notice = new DingTalkTeacherLiveNotice();
            $notice->handle($params);
        }
    }

    public function createTask(ChapterLiveModel $live)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        if (!$weworkNoticeEnabled && !$dingtalkNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $live->id;
        $task->item_type = TaskModel::TYPE_STAFF_NOTICE_TEACHER_LIVE;
        $task->priority = TaskModel::PRIORITY_LOW;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

}
