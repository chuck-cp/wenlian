<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Http\Api\Services\Common as CommonService;
use App\Traits\Response as ResponseTrait;

/**
 * @RoutePrefix("/api")
 */
class PublicController extends Controller
{

    use ResponseTrait;

    /**
     * @Options("/{match:(.*)}", name="api.match_options")
     */
    public function corsAction()
    {
        $this->response->setStatusCode(204);

        return $this->response;
    }

    /**
     * @Get("/now", name="api.public.now")
     */
    public function nowAction()
    {
        return $this->jsonSuccess(['now' => time()]);
    }

    /**
     * @Get("/license/info", name="api.public.license_info")
     */
    public function licenseInfoAction()
    {
        $service = new CommonService();

        $license = $service->getMyLicenseInfo();

        return $this->jsonSuccess(['license' => $license]);
    }

    /**
     * @Get("/socket/info", name="api.public.socket_info")
     */
    public function socketInfoAction()
    {
        $service = new CommonService();

        $content = $service->getSocketInfo();

        return $this->jsonSuccess(['socket' => $content]);
    }

}
