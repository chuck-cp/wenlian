<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\WeWork;

use App\Services\WeWorkNotice;

class ServerMonitor extends WeWorkNotice
{

    public function handle(array $params)
    {
        $notice = new WeWorkNotice();

        $notice->atTechSupport($params['content']);
    }

}