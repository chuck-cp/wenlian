<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\Task as TaskModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\Sms\OrderFinish as SmsOrderFinishNotice;
use App\Services\Logic\Notice\External\WeChat\OrderFinish as WeChatOrderFinishNotice;
use App\Services\Logic\Service as LogicService;

class PaperGradeFinish extends LogicService
{

    public function handleTask(TaskModel $task)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        $paperUserId = $task->item_id;

        $paperUserRepo = new ExamPaperUserRepo();

        $paperUser = $paperUserRepo->findById($paperUserId);

        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($paperUser->paper_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($paperUser->user_id);

        $params = [
            'paper_user' => [
                'paper_score' => $paperUser->paper_score,
                'user_score' => $paperUser->user_score,
            ],
            'paper' => [
                'id' => $paper->id,
                'title' => $paper->title,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ];

        if ($wechatNoticeEnabled) {
            $notice = new WeChatOrderFinishNotice();
            $notice->handle($params);
        }

        if ($smsNoticeEnabled) {
            $notice = new SmsOrderFinishNotice();
            $notice->handle($params);
        }
    }

    public function createTask(ExamPaperUserModel $paperUser)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $paperUser->id;
        $task->item_type = TaskModel::TYPE_NOTICE_PAPER_GRADE_FINISH;
        $task->priority = TaskModel::PRIORITY_HIGH;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    public function wechatNoticeEnabled()
    {
        $oa = $this->getSettings('wechat.oa');

        if ($oa['enabled'] == 0) return false;

        $template = json_decode($oa['notice_template'], true);

        $result = $template['paper_grade_finish']['enabled'] ?? 0;

        return $result == 1;
    }

    public function smsNoticeEnabled()
    {
        $sms = $this->getSettings('sms');

        $template = json_decode($sms['template'], true);

        $result = $template['paper_grade_finish']['enabled'] ?? 0;

        return $result == 1;
    }

}
