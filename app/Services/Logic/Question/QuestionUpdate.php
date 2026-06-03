<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Question;

use App\Models\Question as QuestionModel;
use App\Services\Logic\QuestionTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\Question as QuestionValidator;

class QuestionUpdate extends LogicService
{

    use QuestionTrait;
    use QuestionDataTrait;

    public function handle($id)
    {
        $post = $this->request->getPost();

        $question = $this->checkQuestion($id);

        $user = $this->getLoginUser();

        $validator = new QuestionValidator();

        $validator->checkOwner($user->id, $question->owner_id);

        $validator->checkIfAllowEdit($question);

        $data = $this->handlePostData($post);

        $data['published'] = $this->getPublishStatus($user, $data['content']);

        $question->assign($data);

        $question->update();

        /**
         * 参数兼容（pc端:xm_tag_ids，移动端:tag_ids）
         */
        if (isset($post['xm_tag_ids'])) {
            $this->saveTags($question, $post['xm_tag_ids']);
        } elseif (isset($post['tag_ids'])) {
            $this->saveTags($question, $post['tag_ids']);
        }

        $this->saveDynamicAttrs($question);

        $this->eventsManager->fire('Question:afterUpdate', $this, $question);

        return $question;
    }

}
