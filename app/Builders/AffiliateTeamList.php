<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

class AffiliateTeamList extends Builder
{

    public function handleUsers(array $relations)
    {
        $users = $this->getUsers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['user'] = $users[$value['user_id']] ?? null;
        }

        return $relations;
    }

    public function getUsers(array $relations)
    {
        $ids = kg_array_column($relations, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
