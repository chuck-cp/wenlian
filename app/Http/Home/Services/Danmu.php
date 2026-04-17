<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Services;

use App\Builders\DanmuList as DanmuListBuilder;
use App\Models\Danmu as DanmuModel;
use App\Models\User as UserModel;
use App\Repos\Danmu as DanmuRepo;
use App\Services\Logic\Comment\CommentDataTrait;
use App\Traits\Client as ClientTrait;
use App\Validators\Danmu as DanmuValidator;
use App\Validators\UserLimit as UserLimitValidator;
use Phalcon\Text;

class Danmu extends Service
{

    use ClientTrait;
    use CommentDataTrait;

    public function getDanmuList()
    {
        $chapterId = $this->request->getQuery('id', 'int', 0);
        $limit = $this->request->getQuery('max', 'int', 1000);

        $where = [
            'chapter_id' => $chapterId,
            'published' => 1,
            'deleted' => 0,
        ];

        $danmuRepo = new DanmuRepo();

        $items = $danmuRepo->findAll($where, 'latest', $limit);

        $result = [];

        if ($items->count() == 0) return $result;

        $danmus = $items->toArray();

        $builder = new DanmuListBuilder();

        $users = $builder->getUsers($danmus);

        foreach ($danmus as $danmu) {

            $danmu['author'] = $users[$danmu['owner_id']]['name'];

            /**
             * 十六进制转十进制
             */
            if (Text::startsWith($danmu['color'], '#')) {
                $danmu['color'] = hexdec(substr($danmu['color'], 1));
            }

            $result[] = [
                $danmu['time'],
                $danmu['type'],
                $danmu['color'],
                $danmu['author'],
                $danmu['text'],
            ];
        }

        return $result;
    }

    public function createDanmu()
    {
        $post = $this->request->getPost();

        $validator = new DanmuValidator();

        $chapter = $validator->checkChapter($post['id']);

        $author = $validator->checkAuthor($post['author']);

        $limitValidator = new UserLimitValidator();

        $limitValidator->checkDailyDanmuLimit($author);

        $danmu = new DanmuModel();

        $danmu->text = $validator->checkText($post['text']);
        $danmu->color = $validator->checkColor($post['color']);
        $danmu->time = $validator->checkTime($post['time']);
        $danmu->type = $validator->checkType($post['type']);
        $danmu->published = $this->getPublishStatus($author, $danmu->text);
        $danmu->client_type = $this->getClientType();
        $danmu->client_ip = $this->getClientIp();
        $danmu->chapter_id = $chapter->id;
        $danmu->owner_id = $author->id;

        $danmu->create();

        $this->incrUserDailyDanmuCount($author);

        return $danmu;
    }

    protected function incrUserDailyDanmuCount(UserModel $user)
    {
        $this->eventsManager->fire('UserDailyCounter:incrDanmuCount', $this, $user);
    }

}
