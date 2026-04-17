<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\CertificateUser as CertificateUserRepo;

class CertificateUser extends Validator
{

    public function checkById($id)
    {
        $certUserRepo = new CertificateUserRepo();

        $certUser = $certUserRepo->findById($id);

        if (!$certUser) {
            throw new BadRequestException('cert_user.not_found');
        }

        return $certUser;
    }

    public function checkBySn($sn)
    {
        $certUserRepo = new CertificateUserRepo();

        $certUser = $certUserRepo->findBySn($sn);

        if (!$certUser) {
            throw new BadRequestException('cert_user.not_found');
        }

        return $certUser;
    }

    public function checkCertUser($certId, $userId)
    {
        $repo = new CertificateUserRepo();

        $certUser = $repo->findCertUser($certId, $userId);

        if (!$certUser) {
            throw new BadRequestException('cert_user.not_found');
        }

        return $certUser;
    }

    public function checkCert($id)
    {
        $validator = new Certificate();

        return $validator->checkCertificate($id);
    }

    public function checkUser($id)
    {
        $validator = new User();

        return $validator->checkUser($id);
    }

}
