<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Builders\AffiliateTeamList as AffiliateTeamListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\UserReferer as UserRefererRepo;
use App\Services\Logic\Service as LogicService;

class AffiliateTeamList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser(true);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['parent_id'] = $user->id;

        if (isset($params['level'])) {
            $params['parent_level'] = $params['level'];
        }

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new UserRefererRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleTeams($pager);
    }

    public function handleTeams($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $relations = $pager->items->toArray();

        $builder = new AffiliateTeamListBuilder();

        $users = $builder->getUsers($relations);

        $items = [];

        foreach ($relations as $relation) {

            $user = $users[$relation['user_id']] ?? new \stdClass();

            $items[] = [
                'id' => $relation['id'],
                'level' => $relation['parent_level'],
                'create_time' => $relation['create_time'],
                'user' => $user,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
