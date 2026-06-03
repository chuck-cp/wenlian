<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Consult;

use App\Models\Consult as ConsultModel;
use App\Services\Utils\ContentAudit as ContentAuditUtil;
use App\Traits\Client as ClientTrait;
use App\Validators\Consult as ConsultValidator;

trait ConsultDataTrait
{

    use ClientTrait;

    protected function handlePostData($post)
    {
        $data = [];

        $data['client_type'] = $this->getClientType();
        $data['client_ip'] = $this->getClientIp();

        $validator = new ConsultValidator();

        if (isset($post['question'])) {
            $data['question'] = $validator->checkQuestion($post['question']);
        }

        if (isset($post['private'])) {
            $data['private'] = $validator->checkPrivateStatus($post['private']);
        }

        return $data;
    }

    protected function getPublishStatus($content)
    {
        $status = ConsultModel::PUBLISH_PENDING;

        $settings = $this->getSettings('security.audit');

        if ($settings['consult_enabled'] == 1) {
            $util = new ContentAuditUtil();
            $result = $util->auditHtml($content);
            if ($result == 0) {
                $status = ConsultModel::PUBLISH_APPROVED;
            } elseif ($result == 1) {
                $status = ConsultModel::PUBLISH_REJECTED;
            }
        }

        return $status;
    }

}
