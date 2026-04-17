<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Certificate;

use App\Repos\Certificate as CertificateRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Service;
use App\Validators\CertificateUser as CertificateUserValidator;

class CertUserInfo extends Service
{

    public function handle($sn)
    {
        $validator = new CertificateUserValidator();

        $relation = $validator->checkBySn($sn);

        $certRepo = new CertificateRepo();

        $cert = $certRepo->findById($relation->cert_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($relation->user_id);

        return [
            'id' => $relation->id,
            'sn' => $relation->sn,
            'deleted' => $relation->deleted,
            'cert_path' => $relation->cert_path,
            'create_time' => $relation->create_time,
            'update_time' => $relation->update_time,
            'cert' => [
                'id' => $cert->id,
                'name' => $cert->name,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ];

    }

}
