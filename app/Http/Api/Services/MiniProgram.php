<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Services;

use App\Services\WeChat as WeChatService;

class MiniProgram extends Service
{

    public function getWeChatSession()
    {
        $code = $this->request->getQuery('code', ['trim', 'string']);

        $wechat = new WeChatService();

        $mp = $wechat->getMiniProgram();

        return $mp->auth->session($code);
    }

}
