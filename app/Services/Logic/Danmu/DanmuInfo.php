<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Danmu;

use App\Models\Danmu as DanmuModel;
use App\Services\Logic\DanmuTrait;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo;

class DanmuInfo extends LogicService
{

    use DanmuTrait;

    public function handle($id)
    {
        $danmu = $this->checkDanmu($id);

        return $this->handleDanmu($danmu);
    }

    protected function handleDanmu(DanmuModel $danmu)
    {
        $owner = $this->handleOwnerInfo($danmu->owner_id);

        return [
            'id' => $danmu->id,
            'text' => $danmu->text,
            'color' => $danmu->color,
            'type' => $danmu->type,
            'time' => $danmu->time,
            'published' => $danmu->published,
            'deleted' => $danmu->deleted,
            'create_time' => $danmu->create_time,
            'update_time' => $danmu->update_time,
            'owner' => $owner,
        ];
    }

    protected function handleOwnerInfo($userId)
    {
        $service = new ShallowUserInfo();

        return $service->handle($userId);
    }

}
