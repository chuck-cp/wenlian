<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Certificate as CertificateService;
use App\Http\Admin\Services\CertificateUser as CertificateUserService;
use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Certificate as CertificateModel;

/**
 * @RoutePrefix("/admin/cert")
 */
class CertificateController extends Controller
{

    /**
     * @Get("/list", name="admin.cert.list")
     */
    public function listAction()
    {
        $service = new CertificateService();

        $pager = $service->getCertificates();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/search", name="admin.cert.search")
     */
    public function searchAction()
    {
        $service = new CertificateService();

        $itemTypes = $service->getItemTypes();
        $grantTypes = $service->getGrantTypes();

        $this->view->setVar('item_types', $itemTypes);
        $this->view->setVar('grant_types', $grantTypes);
    }

    /**
     * @Get("/add", name="admin.cert.add")
     */
    public function addAction()
    {
        $service = new CertificateService();

        $itemTypes = $service->getItemTypes();
        $grantTypes = $service->getGrantTypes();

        $this->view->setVar('item_types', $itemTypes);
        $this->view->setVar('grant_types', $grantTypes);
    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.cert.edit")
     */
    public function editAction($id)
    {
        $service = new CertificateService();

        $cert = $service->getCertificate($id);
        $xmCourses = $service->getXmCourses($cert);
        $xmExamPapers = $service->getXmExamPapers($cert);
        $xmTopics = $service->getXmTopics($cert);
        $itemTypes = $service->getItemTypes();
        $grantTypes = $service->getGrantTypes();

        $this->view->setVar('cert', $cert);
        $this->view->setVar('xm_courses', $xmCourses);
        $this->view->setVar('xm_exam_papers', $xmExamPapers);
        $this->view->setVar('xm_topics', $xmTopics);
        $this->view->setVar('item_types', $itemTypes);
        $this->view->setVar('grant_types', $grantTypes);
    }

    /**
     * @Post("/create", name="admin.cert.create")
     */
    public function createAction()
    {
        $service = new CertificateService();

        $certificate = $service->createCertificate();

        $location = $this->url->get([
            'for' => 'admin.cert.edit',
            'id' => $certificate->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加证书成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.cert.update")
     */
    public function updateAction($id)
    {
        $service = new CertificateService();

        $service->updateCertificate($id);

        $location = $this->url->get(['for' => 'admin.cert.list']);

        $content = [
            'location' => $location,
            'msg' => '更新证书成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.cert.delete")
     */
    public function deleteAction($id)
    {
        $service = new CertificateService();

        $service->deleteCertificate($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除证书成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.cert.restore")
     */
    public function restoreAction($id)
    {
        $service = new CertificateService();

        $service->restoreCertificate($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '还原证书成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/users", name="admin.cert.users")
     */
    public function usersAction($id)
    {
        $service = new CertificateService();
        $cert = $service->getCertificate($id);

        $service = new CertificateUserService();
        $pager = $service->getUsers($id);

        $this->view->setVar('cert', $cert);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/user/search", name="admin.cert.search_user")
     */
    public function searchUserAction($id)
    {
        $service = new CertificateService();
        $cert = $service->getCertificate($id);

        $this->view->pick('certificate/search_user');
        $this->view->setVar('cert', $cert);
    }

    /**
     * @Route("/{id:[0-9]+}/grant", name="admin.cert.grant")
     */
    public function grantAction($id)
    {
        $certService = new CertificateService();
        $cert = $certService->getCertificate($id);

        if ($cert->grant_type != CertificateModel::GRANT_TYPE_MANUAL) {
            throw new BadRequestException('certificate.manual_grant_not_allowed');
        }

        $service = new CertificateUserService();

        if ($this->request->isPost()) {

            $service->grant($id);

            $location = $this->url->get([
                'for' => 'admin.cert.users',
                'id' => $id,
            ]);

            $content = [
                'location' => $location,
                'msg' => '发放证书成功',
            ];

            return $this->jsonSuccess($content);

        } else {

            $this->view->pick('certificate/grant');
            $this->view->setVar('cert', $cert);
        }
    }

    /**
     * @Post("/user/delete/{id:[0-9]+}", name="admin.cert.delete_user")
     */
    public function deleteUserAction($id)
    {
        $service = new CertificateUserService();

        $service->delete($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '撤销授予成功',
        ];

        return $this->jsonSuccess($content);
    }

}
