<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Koogua as KooguaService;
use App\Library\AppInfo as AppInfo;
use App\Services\Service as AppService;
use App\Traits\Response as ResponseTrait;

/**
 * @RoutePrefix("/admin/koogua")
 */
class KooguaController extends \Phalcon\Mvc\Controller
{

    use ResponseTrait;

    /**
     * @Route("/license", name="admin.koogua.license")
     */
    public function licenseAction()
    {
        if ($this->request->isPost()) {

            $service = new KooguaService();

            $service->saveLicence();

            $location = $this->url->get(['for' => 'admin.login']);

            $content = [
                'location' => $location,
                'msg' => '授权成功',
            ];

            return $this->jsonSuccess($content);
        }

        $service = new AppService();

        $siteInfo = $service->getSettings('site');

        $appInfo = new AppInfo();

        $this->view->setVar('app_info', $appInfo);
        $this->view->setVar('site_info', $siteInfo);
    }

    /**
     * @Get("/wiki", name="admin.koogua.wiki")
     */
    public function wikiAction()
    {
        $url = 'https://www.koogua.com/page/wiki';

        return $this->response->redirect($url, true);
    }

    /**
     * @Get("/ticket", name="admin.koogua.ticket")
     */
    public function ticketAction()
    {
        $service = new KooguaService();

        $license = $service->getLicense();

        $url = sprintf('https://www.koogua.com/koogua/ticket?sn=%s', $license['sn']);

        return $this->response->redirect($url, true);
    }

}
