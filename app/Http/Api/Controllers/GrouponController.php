<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Groupon\GrouponInfo as GrouponInfoService;
use App\Services\Logic\Groupon\GrouponList as GrouponListService;
use App\Services\Logic\Groupon\OrderCreate as OrderCreateService;
use App\Services\Logic\Groupon\TeamList as GrouponTeamListService;

/**
 * @RoutePrefix("/api/groupon")
 */
class GrouponController extends Controller
{

    /**
     * @Get("/list", name="api.groupon.list")
     */
    public function listAction()
    {
        $service = new GrouponListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/{id:[0-9]+}/info", name="api.groupon.info")
     */
    public function infoAction($id)
    {
        $service = new GrouponInfoService();

        $groupon = $service->handle($id);

        if ($groupon['deleted'] == 1) {
            $this->notFound();
        }

        if ($groupon['published'] == 0) {
            $this->notFound();
        }

        return $this->jsonSuccess(['groupon' => $groupon]);
    }

    /**
     * @Get("/{id:[0-9]+}/teams", name="api.groupon.teams")
     */
    public function teamsAction($id)
    {
        $service = new GrouponTeamListService();

        $pager = $service->handle($id);

        return $this->jsonPaginate($pager);
    }

    /**
     * @Post("/{id:[0-9]+}/order", name="api.groupon.order")
     */
    public function orderAction($id)
    {
        $service = new OrderCreateService();

        $order = $service->run($id);

        return $this->jsonSuccess(['order' => $order]);
    }

}
