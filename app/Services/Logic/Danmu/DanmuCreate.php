<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Danmu;

use App\Models\Danmu as DanmuModel;
use App\Models\User as UserModel;
use App\Services\Logic\ChapterTrait;
use App\Services\Logic\Comment\CommentDataTrait;
use App\Services\Logic\Service as LogicService;
use App\Traits\Client as ClientTrait;
use App\Validators\Danmu as DanmuValidator;
use App\Validators\UserLimit as UserLimitValidator;

class DanmuCreate extends LogicService
{

    use ChapterTrait;
    use ClientTrait;
    use CommentDataTrait;

    public function handle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $chapter = $this->checkChapter($post['chapter_id']);

        $validator = new UserLimitValidator();

        $validator->checkDailyDanmuLimit($user);

        $validator = new DanmuValidator();

        $danmu = new DanmuModel();

        $danmu->text = $validator->checkText($post['text']);
        $danmu->color = $validator->checkColor($post['color']);
        $danmu->time = $validator->checkTime($post['time']);
        $danmu->published = $this->getPublishStatus($user, $danmu->text);
        $danmu->client_type = $this->getClientType();
        $danmu->client_ip = $this->getClientIp();
        $danmu->chapter_id = $chapter->id;
        $danmu->owner_id = $user->id;

        $danmu->create();

        $this->incrUserDailyDanmuCount($user);

        return $danmu;
    }

    protected function incrUserDailyDanmuCount(UserModel $user)
    {
        $this->eventsManager->fire('UserDailyCounter:incrDanmuCount', $this, $user);
    }

}
