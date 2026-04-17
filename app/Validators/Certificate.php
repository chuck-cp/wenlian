<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Certificate as CertificateModel;
use App\Repos\Certificate as CertificateRepo;

class Certificate extends Validator
{

    public function checkCertificate($id)
    {
        $certRepo = new CertificateRepo();

        $cert = $certRepo->findById($id);

        if (!$cert) {
            throw new BadRequestException('certificate.not_found');
        }

        return $cert;
    }

    public function checkName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2) {
            throw new BadRequestException('certificate.name_too_short');
        }

        if ($length > 30) {
            throw new BadRequestException('certificate.name_too_long');
        }

        return $value;
    }

    public function checkItemType($type)
    {
        $list = CertificateModel::itemTypes();

        if (!array_key_exists($type, $list)) {
            throw new BadRequestException('certificate.invalid_item_type');
        }

        return $type;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('certificate.invalid_publish_status');
        }

        return $status;
    }

    public function checkCourse($id)
    {
        $validator = new Course();

        return $validator->checkCourse($id);
    }

    public function checkExamPaper($id)
    {
        $validator = new ExamPaper();

        return $validator->checkExamPaper($id);
    }

    public function checkTopic($id)
    {
        $validator = new Topic();

        return $validator->checkTopic($id);
    }

}
