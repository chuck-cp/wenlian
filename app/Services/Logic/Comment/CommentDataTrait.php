<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Comment;

use App\Models\Comment as CommentModel;
use App\Models\User as UserModel;
use App\Services\Utils\ContentAudit as ContentAuditUtil;
use App\Traits\Client as ClientTrait;
use App\Validators\Comment as CommentValidator;

trait CommentDataTrait
{

    use ClientTrait;

    protected function handlePostData($post)
    {
        $data = [];

        $data['client_type'] = $this->getClientType();
        $data['client_ip'] = $this->getClientIp();

        $validator = new CommentValidator();

        $data['content'] = $validator->checkContent($post['content']);

        return $data;
    }

    protected function getPublishStatus(UserModel $user, $content)
    {
        $case1 = $user->question_count > 100;
        $case2 = $user->answer_count > 100;
        $case3 = $user->comment_count > 100;

        $status = CommentModel::PUBLISH_PENDING;

        if ($case1 || $case2 || $case3) {
            $status = CommentModel::PUBLISH_APPROVED;
        }

        $settings = $this->getSettings('security.audit');

        if ($settings['comment_enabled'] == 1) {
            $util = new ContentAuditUtil();
            $result = $util->auditHtml($content);
            if ($result == 0) {
                $status = CommentModel::PUBLISH_APPROVED;
            } elseif ($result == 1) {
                $status = CommentModel::PUBLISH_REJECTED;
            } else {
                $status = CommentModel::PUBLISH_PENDING;
            }
        }

        return $status;
    }

}
