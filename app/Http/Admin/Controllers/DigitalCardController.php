<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\DigitalCard as DigitalCardService;
use App\Http\Admin\Services\DigitalCardExport as DigitalCardExportService;

/**
 * @RoutePrefix("/admin/digital/card")
 */
class DigitalCardController extends Controller
{

    /**
     * @Get("/list", name="admin.digital_card.list")
     */
    public function listAction()
    {
        $service = new DigitalCardService();

        $pager = $service->getDigitalCards();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/search", name="admin.digital_card.search")
     */
    public function searchAction()
    {
        $service = new DigitalCardService();

        $xmVips = $service->getXmVips();
        $xmCourses = $service->getXmCourses();
        $xmPackages = $service->getXmPackages();
        $xmExamPapers = $service->getXmExamPapers();
        $xmArticles = $service->getXmArticles();
        $itemTypes = $service->getItemTypes();

        $this->view->setVar('xm_vips', $xmVips);
        $this->view->setVar('xm_courses', $xmCourses);
        $this->view->setVar('xm_packages', $xmPackages);
        $this->view->setVar('xm_exam_papers', $xmExamPapers);
        $this->view->setVar('xm_articles', $xmArticles);
        $this->view->setVar('item_types', $itemTypes);
    }

    /**
     * @Get("/export", name="admin.digital_card.export")
     */
    public function exportAction()
    {
        $exportService = new DigitalCardExportService();

        $result = $exportService->handle();

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.digital_card.search'],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/add", name="admin.digital_card.add")
     */
    public function addAction()
    {
        $service = new DigitalCardService();

        $xmVips = $service->getXmVips();
        $xmCourses = $service->getXmCourses();
        $xmPackages = $service->getXmPackages();
        $xmExamPapers = $service->getXmExamPapers();
        $xmArticles = $service->getXmArticles();
        $itemTypes = $service->getItemTypes();

        $this->view->setVar('xm_vips', $xmVips);
        $this->view->setVar('xm_courses', $xmCourses);
        $this->view->setVar('xm_packages', $xmPackages);
        $this->view->setVar('xm_exam_papers', $xmExamPapers);
        $this->view->setVar('xm_articles', $xmArticles);
        $this->view->setVar('item_types', $itemTypes);
    }

    /**
     * @Post("/create", name="admin.digital_card.create")
     */
    public function createAction()
    {
        $service = new DigitalCardService();

        $service->createDigitalCard();

        $location = $this->url->get([
            'for' => 'admin.digital_card.list',
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加兑换码成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.digital_card.delete")
     */
    public function deleteAction($id)
    {
        $service = new DigitalCardService();

        $service->deleteDigitalCard($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '作废兑换码成功',
        ];

        return $this->jsonSuccess($content);
    }

}
