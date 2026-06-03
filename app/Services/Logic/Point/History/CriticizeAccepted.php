<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Point\History;

use App\Models\PointHistory as PointHistoryModel;
use App\Models\Report as ReportModel;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Repos\PointHistory as PointHistoryRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Point\PointHistory;

class CriticizeAccepted extends PointHistory
{

    public function handle(ReportModel $report)
    {
        $setting = $this->getSettings('point');

        $pointEnabled = $setting['enabled'] ?? 0;

        if ($pointEnabled == 0) return;

        $eventRule = json_decode($setting['event_rule'], true);

        $eventEnabled = $eventRule['criticize_accepted']['enabled'] ?? 0;

        if ($eventEnabled == 0) return;

        $eventPoint = $eventRule['criticize_accepted']['point'] ?? 0;

        if ($eventPoint <= 0) return;

        $eventId = $report->item_id;

        $eventType = PointHistoryModel::EVENT_CRITICIZE_ACCEPTED;

        $historyRepo = new PointHistoryRepo();

        $history = $historyRepo->findEventHistory($eventId, $eventType);

        if ($history) return;

        $userRepo = new UserRepo();

        $user = $userRepo->findById($report->owner_id);

        $questionRepo = new ExamQuestionRepo();

        $question = $questionRepo->findById($report->item_id);

        $eventInfo = [
            'question' => [
                'id' => $question->id,
                'topic' => kg_substr(strip_tags($question->topic), 0, 50),
            ]
        ];

        $history = new PointHistoryModel();

        $history->user_id = $user->id;
        $history->user_name = $user->name;
        $history->event_id = $eventId;
        $history->event_type = $eventType;
        $history->event_info = $eventInfo;
        $history->event_point = $eventPoint;

        $this->handlePointHistory($history);
    }

}
