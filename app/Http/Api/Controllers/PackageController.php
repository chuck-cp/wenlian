<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Package\CourseList as PackageCourseListService;
use App\Services\Logic\Package\PackageInfo as PackageInfoService;

/**
 * @RoutePrefix("/api/package")
 */
class PackageController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.package.info")
     */
    public function infoAction($id)
    {
        $service = new PackageInfoService();

        $package = $service->handle($id);

        if ($package['deleted'] == 1) {
            $this->notFound();
        }

        if ($package['published'] == 0) {
            $this->notFound();
        }

        return $this->jsonSuccess(['package' => $package]);
    }

    /**
     * @Get("/{id}/courses", name="api.package.courses")
     */
    public function coursesAction($id)
    {
        $service = new PackageCourseListService();

        $courses = $service->handle($id);

        return $this->jsonSuccess(['courses' => $courses]);
    }

}
