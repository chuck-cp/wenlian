<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\Distribution as DistributionModel;
use App\Repos\Distribution as DistributionRepo;

class Distribution extends Validator
{

    public function checkDistribution($id)
    {
        $distRepo = new DistributionRepo();

        $dist = $distRepo->findById($id);

        if (!$dist) {
            throw new BadRequestException('distribution.not_found');
        }

        return $dist;
    }

    public function checkItemType($itemType)
    {
        if (!array_key_exists($itemType, DistributionModel::itemTypes())) {
            throw new BadRequestException('distribution.invalid_item_type');
        }

        return $itemType;
    }

    public function checkItemIds($itemIds)
    {
        if (empty($itemIds)) {
            throw new BadRequestException('distribution.item_required');
        }

        return explode(',', $itemIds);
    }

    public function checkV1ComRate($rate)
    {
        $value = $this->filter->sanitize($rate, ['trim', 'int']);

        if ($value < 1 || $value > 30) {
            throw new BadRequestException('distribution.invalid_v1_com_rate');
        }

        return $value;
    }

    public function checkV2ComRate($rate)
    {
        $value = $this->filter->sanitize($rate, ['trim', 'int']);

        if ($value < 1 || $value > 20) {
            throw new BadRequestException('distribution.invalid_v2_com_rate');
        }

        return $value;
    }

    public function checkV3ComRate($rate)
    {
        $value = $this->filter->sanitize($rate, ['trim', 'int']);

        if ($value < 1 || $value > 10) {
            throw new BadRequestException('distribution.invalid_v3_com_rate');
        }

        return $value;
    }

    public function checkStartTime($startTime)
    {
        if (!CommonValidator::date($startTime, 'Y-m-d H:i:s')) {
            throw new BadRequestException('distribution.invalid_start_time');
        }

        return strtotime($startTime);
    }

    public function checkEndTime($endTime)
    {
        if (!CommonValidator::date($endTime, 'Y-m-d H:i:s')) {
            throw new BadRequestException('distribution.invalid_end_time');
        }

        return strtotime($endTime);
    }

    public function checkTimeRange($startTime, $endTime)
    {
        if ($startTime >= $endTime) {
            throw new BadRequestException('distribution.invalid_time_range');
        }
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('distribution.invalid_publish_status');
        }

        return $status;
    }

}
