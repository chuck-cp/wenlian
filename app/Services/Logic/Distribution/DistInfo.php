<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Distribution;

use App\Models\Distribution as DistributionModel;
use App\Services\Logic\DistributionTrait;
use App\Services\Logic\Service as LogicService;

class DistInfo extends LogicService
{

    use DistributionTrait;
    use DistInfoTrait;

    public function handle($id)
    {
        $dist = $this->checkDistribution($id);

        return $this->handleDistribution($dist);
    }

    protected function handleDistribution(DistributionModel $dist)
    {
        $this->cosUrl = kg_cos_url();

        $item = $this->handleItemInfo($dist->item_type, $dist->item_info);

        return [
            'id' => $dist->id,
            'v1_com_rate' => $dist->v1_com_rate,
            'v2_com_rate' => $dist->v2_com_rate,
            'v3_com_rate' => $dist->v3_com_rate,
            'start_time' => $dist->start_time,
            'end_time' => $dist->end_time,
            'create_time' => $dist->create_time,
            'update_time' => $dist->update_time,
            'published' => $dist->published,
            'deleted' => $dist->deleted,
            'item' => $item,
        ];
    }

}
