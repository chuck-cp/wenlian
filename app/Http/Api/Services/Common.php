<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Services;

use App\Models\Account as AccountModel;
use App\Services\Service as AppService;

class Common extends Service
{

    public function getSocketInfo()
    {
        $service = new AppService();

        $websocket = $service->getConfig()->get('websocket');

        $content = [];

        /**
         * ssl通过nginx转发实现
         */
        if ($this->request->isSecure()) {
            list($domain) = explode(':', $websocket->connect_address);
            $content['connect_url'] = sprintf('wss://%s/wss', $domain);
        } else {
            $content['connect_url'] = sprintf('ws://%s', $websocket->connect_address);
        }

        $content['ping_interval'] = $websocket->ping_interval;

        return $content;
    }

}
