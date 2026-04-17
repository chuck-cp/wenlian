<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Models\User as UserModel;
use App\Services\Logic\Live\LiveManage as LiveManageService;

class LiveChat extends Validator
{

    public function checkMessage($content)
    {
        $value = $this->filter->sanitize($content, ['trim', 'striptags']);

        $length = kg_strlen($value);

        if ($length < 1) {
            throw new BadRequestException('live_chat.msg_too_short');
        }

        if ($length > 255) {
            throw new BadRequestException('live_chat.msg_too_long');
        }

        return $value;
    }

    public function checkIfAllowPost(ChapterLiveModel $live, UserModel $user)
    {
        if ($live->settings['chat_enabled'] == 0) {
            throw new BadRequestException('live_chat.chat_disabled');
        }

        $service = new LiveManageService();

        if ($service->isBlocked($live->course_id, $user->id)) {
            throw new BadRequestException('live_chat.user_blocked');
        }
    }

    public function checkIfAllowManage(ChapterLiveModel $live, UserModel $user)
    {
        $validator = new Course();

        $course = $validator->checkCourse($live->course_id);

        if ($user->id != $course->teacher_id) {
            throw new BadRequestException('live_chat.no_manage_priv');
        }
    }

}
