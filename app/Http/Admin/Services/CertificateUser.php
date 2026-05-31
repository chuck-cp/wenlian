<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\CertificateUserList as CertificateUserListBuilder;
use App\Exceptions\BadRequest as BadRequestException;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Certificate as CertificateModel;
use App\Models\CertificateUser as CertificateUserModel;
use App\Repos\Certificate as CertificateRepo;
use App\Repos\CertificateUser as CertificateUserRepo;
use App\Services\Logic\Certificate\Poster as CertPosterService;
use App\Validators\Certificate as CertificateValidator;
use App\Validators\CertificateUser as CertificateUserValidator;
use Throwable;

class CertificateUser extends Service
{

    use AccountSearchTrait;

    public function delete($id)
    {
        $validator = new CertificateUserValidator();

        $certUser = $validator->checkById($id);

        $certUser->deleted = 1;

        $certUser->update();

        $cert = $this->findCertOrFail($certUser->cert_id);

        $this->recountGrants($cert);

        return $certUser;
    }

    public function grant($id)
    {
        $validator = new CertificateValidator();

        $cert = $validator->checkCertificate($id);

        if ($cert->grant_type != CertificateModel::GRANT_TYPE_MANUAL) {
            throw new BadRequestException('certificate.manual_grant_not_allowed');
        }

        $post = $this->request->getPost();

        $certUserValidator = new CertificateUserValidator();
        $user = $certUserValidator->checkUser($post['user_id']);

        $certUserRepo = new CertificateUserRepo();
        $activeCertUser = $certUserRepo->findCertUser($cert->id, $user->id);

        if ($activeCertUser) {
            throw new BadRequestException('cert_user.already_granted');
        }

        $deletedCertUser = CertificateUserModel::findFirst([
            'conditions' => 'cert_id = :cert_id: AND user_id = :user_id: AND deleted = 1',
            'bind' => [
                'cert_id' => $cert->id,
                'user_id' => $user->id,
            ],
            'order' => 'id DESC',
        ]);

        if ($deletedCertUser) {
            $deletedCertUser->deleted = 0;
            $deletedCertUser->update();
            $certUser = $deletedCertUser;
        } else {
            $certUser = new CertificateUserModel();
            $certUser->cert_id = $cert->id;
            $certUser->user_id = $user->id;
            $certUser->create();
        }

        $this->generateCertImage($certUser);

        $this->recountGrants($cert);
    }

    public function getUsers($id)
    {
        $cert = $this->findCertOrFail($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['cert_id'] = $cert->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new CertificateUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleUsers($pager);
    }

    protected function recountGrants(CertificateModel $cert)
    {
        $certRepo = new CertificateRepo();

        $grantCount = $certRepo->countGrants($cert->id);

        $cert->grant_count = $grantCount;

        $cert->update();
    }

    protected function findCertOrFail($id)
    {
        $validator = new CertificateValidator();

        return $validator->checkCertificate($id);
    }

    protected function generateCertImage(CertificateUserModel $certUser)
    {
        $service = new CertPosterService();

        try {
            $service->handle($certUser->sn);
        } catch (Throwable $e) {
            $this->getDI()->get('logger')->error(sprintf(
                'Generate Certificate Image Failed, sn: %s, message: %s',
                $certUser->sn,
                $e->getMessage()
            ));
        }
    }

    protected function handleUsers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new CertificateUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleUsers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
