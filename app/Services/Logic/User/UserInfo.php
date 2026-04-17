<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User;

use App\Models\User as UserModel;
use App\Services\Logic\ContentTrait;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\UserTrait;

class UserInfo extends LogicService
{

    use UserTrait;
    use ContentTrait;

    public function handle($id)
    {
        $user = $this->checkUser($id);

        return $this->handleUser($user);
    }

    protected function handleUser(UserModel $user)
    {
        $profile = $this->handleContent($user->profile);

        $area = str_replace('/', ' / ', $user->area);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'title' => $user->title,
            'about' => $user->about,
            'profile' => $profile,
            'area' => $area,
            'gender' => $user->gender,
            'vip' => $user->vip,
            'locked' => $user->locked,
            'deleted' => $user->deleted,
            'study_course_count' => $user->study_course_count,
            'study_paper_count' => $user->study_paper_count,
            'study_article_count' => $user->study_article_count,
            'question_count' => $user->question_count,
            'answer_count' => $user->answer_count,
            'comment_count' => $user->comment_count,
            'favorite_count' => $user->favorite_count,
            'vip_expiry_time' => $user->vip_expiry_time,
            'lock_expiry_time' => $user->lock_expiry_time,
            'active_time' => $user->active_time,
            'create_time' => $user->create_time,
            'update_time' => $user->update_time,
        ];
    }

}
