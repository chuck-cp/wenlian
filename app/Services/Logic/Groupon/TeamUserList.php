<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Builders\GrouponTeamUserList as GrouponTeamUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\GrouponTeamUser as GrouponTeamUserRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\GrouponTeam as GrouponTeamValidator;

class TeamUserList extends LogicService
{

    public function handle($id)
    {
        $validator = new GrouponTeamValidator();

        $team = $validator->checkGrouponTeam($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['team_id'] = $team->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GrouponTeamUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new GrouponTeamUserListBuilder();

        $relations = $pager->items->toArray();

        $users = $builder->getUsers($relations);

        $items = [];

        foreach ($relations as $relation) {

            $user = $users[$relation['user_id']] ?? new \stdClass();

            $items[] = [
                'id' => $relation->id,
                'status' => $relation['status'],
                'create_time' => $relation['create_time'],
                'update_time' => $relation['update_time'],
                'user' => $user,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
