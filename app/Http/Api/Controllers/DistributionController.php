<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Distribution\Poster as DistPosterService;
use App\Services\Logic\Distribution\DistList as DistListService;

/**
 * @RoutePrefix("/api/dist")
 */
class DistributionController extends Controller
{

    /**
     * @Get("/list", name="api.dist.list")
     */
    public function listAction()
    {
        $service = new DistListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/{id:[0-9]+}/poster", name="api.dist.poster")
     */
    public function posterAction($id)
    {
        $service = new DistPosterService();

        $image = $service->handle($id);

        $this->response->setContentType('image/png');
        $this->response->setContent($image->encode('png'));

        return $this->response;
    }

}
