<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Models\Groupon as GrouponModel;
use App\Services\Logic\GrouponTrait;
use App\Services\Logic\Service as LogicService;

class GrouponInfo extends LogicService
{

    use GrouponTrait;
    use GrouponInfoTrait;

    public function handle($id)
    {
        $groupon = $this->checkGroupon($id);

        return $this->handleGroupon($groupon);
    }

    protected function handleGroupon(GrouponModel $groupon)
    {
        $this->cosUrl = kg_cos_url();

        $item = $this->handleItemInfo($groupon->item_type, $groupon->item_info);

        return [
            'id' => $groupon->id,
            'member_price' => (float)$groupon->member_price,
            'leader_price' => (float)$groupon->leader_price,
            'partner_limit' => $groupon->partner_limit,
            'partner_expiry' => $groupon->partner_expiry,
            'virtual_partner' => $groupon->virtual_partner,
            'total_team_count' => $groupon->total_team_count,
            'finish_team_count' => $groupon->finish_team_count,
            'start_time' => $groupon->start_time,
            'end_time' => $groupon->end_time,
            'create_time' => $groupon->create_time,
            'update_time' => $groupon->update_time,
            'published' => $groupon->published,
            'deleted' => $groupon->deleted,
            'item' => $item,
        ];
    }

}
