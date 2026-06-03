<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Review;

use App\Models\Review as ReviewModel;
use App\Models\User as UserModel;
use App\Services\Utils\ContentAudit as ContentAuditUtil;
use App\Traits\Client as ClientTrait;
use App\Validators\Review as ReviewValidator;

trait ReviewDataTrait
{

    use ClientTrait;

    protected function handlePostData($post)
    {
        $data = [];

        $data['client_type'] = $this->getClientType();
        $data['client_ip'] = $this->getClientIp();

        $validator = new ReviewValidator();

        $data['content'] = $validator->checkContent($post['content']);
        $data['rating1'] = $validator->checkRating($post['rating1']);
        $data['rating2'] = $validator->checkRating($post['rating2']);
        $data['rating3'] = $validator->checkRating($post['rating3']);

        if (isset($post['anonymous'])) {
            $data['anonymous'] = $validator->checkAnonymous($post['anonymous']);
        }

        return $data;
    }

    protected function getPublishStatus($content)
    {
        $status = ReviewModel::PUBLISH_PENDING;

        $settings = $this->getSettings('security.audit');

        if ($settings['review_enabled'] == 1) {
            $util = new ContentAuditUtil();
            $result = $util->auditHtml($content);
            if ($result == 0) {
                $status = ReviewModel::PUBLISH_APPROVED;
            } elseif ($result == 1) {
                $status = ReviewModel::PUBLISH_REJECTED;
            }
        }

        return $status;
    }

}
