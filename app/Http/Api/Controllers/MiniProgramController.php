<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Http\Api\Services\MiniProgram as MiniProgramService;

/**
 * @RoutePrefix("/api/mp")
 */
class MiniProgramController extends Controller
{

    /**
     * @Get("/wechat/session", name="api.mp.wechat_session")
     */
    public function wechatSessionAction()
    {
        $mpService = new MiniProgramService();

        $session = $mpService->getWeChatSession();

        return $this->jsonSuccess(['session' => $session]);
    }

}
