<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Http\Home\Services\Danmu as DanmuService;
use App\Services\Logic\Danmu\DanmuInfo as DanmuInfoService;

/**
 * @RoutePrefix("/danmu/v3")
 */
class DanmuController extends Controller
{

    /**
     * @Get("/", name="home.danmu.list")
     */
    public function listAction()
    {
        $service = new DanmuService();

        $data = $service->getDanmuList();

        return $this->jsonSuccess(['data' => $data]);
    }

    /**
     * @Post("/", name="home.danmu.create")
     */
    public function createAction()
    {
        $service = new DanmuService();

        $danmu = $service->createDanmu();

        $service = new DanmuInfoService();

        $danmu = $service->handle($danmu->id);

        return $this->jsonSuccess(['danmu' => $danmu]);
    }

}
