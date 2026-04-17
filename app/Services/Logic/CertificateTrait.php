<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use App\Validators\Certificate as CertificateValidator;

trait CertificateTrait
{

    public function checkCert($id)
    {
        $validator = new CertificateValidator();

        return $validator->checkCertificate($id);
    }

}
