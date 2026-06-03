<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use App\Validators\Groupon as GrouponValidator;

trait GrouponTrait
{

    public function checkGroupon($id)
    {
        $validator = new GrouponValidator();

        return $validator->checkGroupon($id);
    }

}
