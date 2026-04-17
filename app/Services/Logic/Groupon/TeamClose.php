<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\GrouponTeamUser as GrouponTeamUserModel;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Services\Logic\Service as LogicService;

class TeamClose extends LogicService
{

    public function handle(GrouponTeamModel $team)
    {
        if ($team->status != GrouponTeamModel::STATUS_PENDING) return;

        $team->status = GrouponTeamModel::STATUS_CLOSED;

        $team->update();

        $teamRepo = new GrouponTeamRepo();

        $teamUsers = $teamRepo->findPendingTeamUsers($team->id);

        if ($teamUsers->count() == 0) return;

        foreach ($teamUsers as $teamUser) {
            $teamUser->status = GrouponTeamUserModel::STATUS_CLOSED;
            $teamUser->update();
        }
    }


}
