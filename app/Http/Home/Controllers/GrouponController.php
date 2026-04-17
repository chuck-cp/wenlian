<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use App\Services\Logic\Groupon\GrouponInfo as GrouponInfoService;
use App\Services\Logic\Groupon\GrouponList as GrouponListService;
use App\Services\Logic\Groupon\OrderCreate as OrderCreateService;
use App\Services\Logic\Groupon\TeamList as GrouponTeamListService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/groupon")
 */
class GrouponController extends Controller
{

    /**
     * @Get("/list", name="home.groupon.list")
     */
    public function listAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getGrouponListUrl();
            return $this->response->redirect($location);
        }

        $this->seo->prependTitle('拼团');
    }

    /**
     * @Get("/pager", name="home.groupon.pager")
     */
    public function pagerAction()
    {
        $service = new GrouponListService();

        $pager = $service->handle();

        $pager->target = 'groupon-list';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('groupon/pager');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}", name="home.groupon.show")
     */
    public function showAction($id)
    {
        $service = new FullH5UrlService();

        /**
         * @todo 移动端适配
         */
        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getHomeUrl();
            return $this->response->redirect($location);
        }

        $service = new GrouponInfoService();

        $groupon = $service->handle($id);

        if ($groupon['deleted'] == 1) {
            $this->notFound();
        }

        if ($groupon['published'] == 0) {
            $this->notFound();
        }

        $this->seo->prependTitle(['拼团', $groupon['item']['title']]);

        $this->view->setVar('groupon', $groupon);
    }

    /**
     * @Get("/{id:[0-9]+}/teams", name="home.groupon.teams")
     */
    public function teamsAction($id)
    {
        $service = new GrouponTeamListService();

        $pager = $service->handle($id);

        $pager->target = 'team-list';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('groupon/teams');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Post("/{id:[0-9]+}/order", name="home.groupon.order")
     */
    public function orderAction($id)
    {
        $service = new OrderCreateService();

        $order = $service->run($id);

        $location = $this->url->get(
            ['for' => 'home.order.pay'],
            ['sn' => $order->sn]
        );

        return $this->jsonSuccess(['location' => $location]);
    }

}
