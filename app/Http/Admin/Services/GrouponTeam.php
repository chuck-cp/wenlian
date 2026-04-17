<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\GrouponTeamList as GrouponTeamListBuilder;
use App\Builders\GrouponTeamUserList as GrouponTeamUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\GrouponTeamUser as GrouponTeamUserRepo;
use App\Services\Logic\Groupon\TeamClose as GrouponTeamCloseService;
use App\Services\Logic\Groupon\TeamRefund as GrouponTeamRefundService;
use App\Validators\Groupon as GrouponValidator;
use App\Validators\GrouponTeam as GrouponTeamValidator;

class GrouponTeam extends Service
{

    public function getTeams($id)
    {
        $groupon = $this->findGrouponOrFail($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['groupon_id'] = $groupon->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GrouponTeamRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        if ($pager->total_items > 0) {

            $builder = new GrouponTeamListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleLeaders($pipeA);
            $pipeC = $builder->objects($pipeB);

            $pager->items = $pipeC;
        }

        return $pager;
    }

    public function getTeamUsers($id)
    {
        $team = $this->findGrouponTeamOrFail($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['team_id'] = $team->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GrouponTeamUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        if ($pager->total_items > 0) {

            $builder = new GrouponTeamUserListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleUsers($pipeA);
            $pipeC = $builder->objects($pipeB);

            $pager->items = $pipeC;
        }

        return $pager;
    }

    public function closeTeam($id)
    {
        $team = $this->findGrouponTeamOrFail($id);

        $validator = new GrouponTeamValidator();

        $validator->checkIfAllowClose($team);

        $service = new GrouponTeamCloseService();

        $service->handle($team);
    }

    public function refundTeam($id)
    {
        $team = $this->findGrouponTeamOrFail($id);

        $validator = new GrouponTeamValidator();

        $validator->checkIfAllowRefund($team);

        $service = new GrouponTeamRefundService();

        $service->handle($team);
    }

    protected function findGrouponOrFail($id)
    {
        $validator = new GrouponValidator();

        return $validator->checkGroupon($id);
    }

    protected function findGrouponTeamOrFail($id)
    {
        $validator = new GrouponTeamValidator();

        return $validator->checkGrouponTeam($id);
    }

}
