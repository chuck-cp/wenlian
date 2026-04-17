<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Builders\GrouponTeamList as GrouponTeamListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Groupon as GrouponModel;
use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\User as UserModel;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\GrouponTeamUser as GrouponTeamUserRepo;
use App\Services\Logic\GrouponTrait;
use App\Services\Logic\Service as LogicService;

class TeamList extends LogicService
{

    use GrouponTrait;

    /**
     * @var GrouponModel
     */
    protected $groupon;

    /**
     * @var UserModel
     */
    protected $user;

    public function handle($id)
    {
        $groupon = $this->checkGroupon($id);

        $this->user = $this->getCurrentUser();

        $this->groupon = $groupon;

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['groupon_id'] = $groupon->id;

        $params['status'] = [
            GrouponTeamModel::STATUS_ACTIVE,
            GrouponTeamModel::STATUS_FINISHED,
        ];

        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $teamRepo = new GrouponTeamRepo();

        $pager = $teamRepo->paginate($params, $sort, $page, $limit);

        return $this->handleTeams($pager);
    }

    protected function handleTeams($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $finished = false;

        if ($this->user->id > 0) {

            $teamUserRepo = new GrouponTeamUserRepo();

            $teamUser = $teamUserRepo->findFinishedGrouponUser($this->groupon->id, $this->user->id);

            if ($teamUser) $finished = true;
        }

        $builder = new GrouponTeamListBuilder();

        $teams = $pager->items->toArray();

        $leaders = $builder->getLeaders($teams);

        $items = [];

        foreach ($teams as $team) {

            $leader = $leaders[$team['leader_id']] ?? new \stdClass();

            $expired = $team['expire_time'] < time();

            $allowJoin = !$finished && !$expired;

            $me = ['allow_join' => $allowJoin];

            $items[] = [
                'id' => $team['id'],
                'groupon_id' => $team['groupon_id'],
                'target_order_count' => $team['target_order_count'],
                'finish_order_count' => $team['finish_order_count'],
                'expire_time' => $team['expire_time'],
                'create_time' => $team['create_time'],
                'update_time' => $team['update_time'],
                'status' => $team['status'],
                'leader' => $leader,
                'me' => $me,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
