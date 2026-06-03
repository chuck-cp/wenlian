<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Answer;

use App\Models\Answer as AnswerModel;
use App\Models\User as UserModel;
use App\Services\Utils\ContentAudit as ContentAuditUtil;
use App\Traits\Client as ClientTrait;
use App\Validators\Answer as AnswerValidator;

trait AnswerDataTrait
{

    use ClientTrait;

    protected function handlePostData($post)
    {
        $data = [];

        $data['client_type'] = $this->getClientType();
        $data['client_ip'] = $this->getClientIp();

        $validator = new AnswerValidator();

        $data['content'] = $validator->checkContent($post['content']);

        if (isset($post['images'])) {
            $data['images'] = $validator->checkImages($post['images']);
        }

        return $data;
    }

    protected function getPublishStatus(UserModel $user, $content)
    {
        $status = $user->answer_count > 100 ? AnswerModel::PUBLISH_APPROVED : AnswerModel::PUBLISH_PENDING;

        $settings = $this->getSettings('security.audit');

        if ($settings['answer_enabled'] == 1) {
            $util = new ContentAuditUtil();
            $result = $util->auditHtml($content);
            if ($result == 0) {
                $status = AnswerModel::PUBLISH_APPROVED;
            } elseif ($result == 1) {
                $status = AnswerModel::PUBLISH_REJECTED;
            } else {
                $status = AnswerModel::PUBLISH_PENDING;
            }
        }

        return $status;
    }

    protected function saveDynamicAttrs(AnswerModel $answer)
    {
        $answer->cover = kg_parse_first_content_image($answer->content);

        $answer->summary = kg_parse_summary($answer->content);

        $answer->update();
    }

}
