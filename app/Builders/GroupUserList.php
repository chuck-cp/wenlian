<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\Group as GroupRepo;

class GroupUserList extends Builder
{

    public function handleGroups($relations)
    {
        $groups = $this->getGroups($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['group'] = $groups[$value['group_id']] ?? null;
        }

        return $relations;
    }

    public function handleUsers($relations)
    {
        $users = $this->getUsers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['user'] = $users[$value['user_id']] ?? null;
        }

        return $relations;
    }

    public function getGroups($relations)
    {
        $ids = kg_array_column($relations, 'group_id');

        $groupRepo = new GroupRepo();

        return $groupRepo->findShallowGroupByIds($ids);
    }

    public function getUsers($relations)
    {
        $ids = kg_array_column($relations, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
