<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Distribution\DistInfo as DistributionInfoService;
use App\Services\Logic\Distribution\DistList as DistributionListService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/distribution")
 */
class DistributionController extends Controller
{

    /**
     * @Get("/list", name="home.distribution.list")
     */
    public function listAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getDistributionListUrl();
            return $this->response->redirect($location);
        }

        $this->seo->prependTitle('分销市场');
    }

    /**
     * @Get("/pager", name="home.distribution.pager")
     */
    public function pagerAction()
    {
        $service = new DistributionListService();

        $pager = $service->handle();

        $pager->target = 'dist-list';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/share", name="home.distribution.share")
     */
    public function shareAction($id)
    {
        $service = new DistributionInfoService();

        $distribution = $service->handle($id);

        $this->view->setVar('distribution', $distribution);
    }

}
