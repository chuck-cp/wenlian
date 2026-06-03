<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Certificate\CertUserInfo as CertUserInfoService;

/**
 * @RoutePrefix("/api/cert")
 */
class CertificateController extends Controller
{

    /**
     * @Get("/user/info", name="api.cert.user_info")
     */
    public function userInfoAction()
    {
        $sn = $this->request->getQuery('sn', ['trim', 'string']);

        $service = new CertUserInfoService();

        $certUser = $service->handle($sn);

        return $this->jsonSuccess(['cert_user' => $certUser]);
    }

}
