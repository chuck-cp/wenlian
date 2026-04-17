<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\Groupon as GrouponRepo;

class GrouponTeamList extends Builder
{

    public function handleGroupons(array $relations)
    {
        $groupons = $this->getGroupons($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['groupon'] = $groupons[$value['groupon_id']] ?? null;
        }

        return $relations;
    }

    public function handleLeaders(array $relations)
    {
        $users = $this->getLeaders($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['leader'] = $users[$value['leader_id']] ?? null;
        }

        return $relations;
    }

    public function getGroupons(array $relations)
    {
        $ids = kg_array_column($relations, 'groupon_id');

        $grouponRepo = new GrouponRepo();

        $columns = [
            'id', 'item_id', 'item_type', 'item_info',
            'member_price', 'leader_price',
            'start_time', 'end_time', 'create_time',
        ];

        $groupons = $grouponRepo->findByIds($ids, $columns);

        $result = [];

        foreach ($groupons->toArray() as $groupon) {
            $groupon['item_info'] = json_decode($groupon['item_info'], true);
            $result[$groupon['id']] = $groupon;
        }

        return $result;
    }

    public function getLeaders(array $relations)
    {
        $ids = kg_array_column($relations, 'leader_id');

        return $this->getShallowUserByIds($ids);
    }

}
