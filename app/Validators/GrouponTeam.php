<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\GrouponTeam as GrouponTeamModel;
use App\Repos\GrouponTeam as GrouponTeamRepo;

class GrouponTeam extends Validator
{

    public function checkGrouponTeam($id)
    {
        $repo = new GrouponTeamRepo();

        $team = $repo->findById($id);

        if (!$team) {
            throw new BadRequestException('groupon_team.not_found');
        }

        return $team;
    }

    public function checkIfAllowClose(GrouponTeamModel $team)
    {
        if ($team->status != GrouponTeamModel::STATUS_PENDING) {
            throw new BadRequestException('groupon_team.close_not_allowed');
        }
    }

    public function checkIfAllowRefund(GrouponTeamModel $team)
    {
        if ($team->status != GrouponTeamModel::STATUS_ACTIVE) {
            throw new BadRequestException('groupon_team.close_not_allowed');
        }
    }

}
