<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use App\Validators\Distribution as DistributionValidator;

trait DistributionTrait
{

    public function checkDistribution($id)
    {
        $validator = new DistributionValidator();

        return $validator->checkDistribution($id);
    }

}
