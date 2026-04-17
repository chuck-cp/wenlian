<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Live;

use App\Builders\LiveBlockList as LiveBlockListBuilder;
use App\Caches\ChapterLive as ChapterLiveCache;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\LiveBlock as LiveBlockModel;
use App\Repos\LiveBlock as LiveBlockRepo;
use App\Services\Logic\ChapterTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\LiveBlock as LiveBlockValidator;
use App\Validators\LiveChat as LiveChatValidator;

class LiveManage extends LogicService
{

    use ChapterTrait;

    public function getBlockedUsers($id)
    {
        $chapter = $this->checkChapter($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['course_id'] = $chapter->course_id;
        $params['expired'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $blockRepo = new LiveBlockRepo();

        $pager = $blockRepo->paginate($params, $sort, $page, $limit);

        return $this->handleBlockedUsers($pager);
    }

    public function updateSettings($id)
    {
        $post = $this->request->getPost();

        $live = $this->checkChapterLive($id);

        $user = $this->getLoginUser();

        $validator = new LiveChatValidator();

        $validator->checkIfAllowManage($live, $user);

        $settings = $live->settings;

        $settings['chat_enabled'] = $post['settings']['chat_enabled'] ?? 1;

        $live->settings = $settings;

        $live->update();

        $this->rebuildChapterLiveCache($id);
    }

    public function blockUser($id)
    {
        $post = $this->request->getPost();

        $live = $this->checkChapterLive($id);

        $me = $this->getLoginUser();

        $validator = new LiveChatValidator();

        $validator->checkIfAllowManage($live, $me);

        $validator = new LiveBlockValidator();

        $course = $validator->checkCourse($live->course_id);
        $user = $validator->checkUser($post['user_id']);
        $expiry = $validator->checkExpiry($post['expiry']);

        $expireTime = strtotime("+{$expiry} days");

        $blockRepo = new LiveBlockRepo();

        $block = $blockRepo->findByCourseUser($course->id, $user->id);

        if (!$block) {

            $block = new LiveBlockModel();

            $block->course_id = $course->id;
            $block->user_id = $user->id;
            $block->expire_time = $expireTime;

            $block->create();

        } else {

            $block->expire_time = $expireTime;

            $block->update();
        }

        $this->addBlockCache($course->id, $user->id);
    }

    public function unblockUser($id)
    {
        $post = $this->request->getPost();

        $live = $this->checkChapterLive($id);

        $me = $this->getLoginUser();

        $validator = new LiveChatValidator();

        $validator->checkIfAllowManage($live, $me);

        $validator = new LiveBlockValidator();

        $course = $validator->checkCourse($live->course_id);
        $user = $validator->checkUser($post['user_id']);
        $block = $validator->checkLiveBlock($course->id, $user->id);

        $block->expire_time = time();

        $block->update();

        $this->removeBlockCache($course->id, $user->id);
    }

    public function isBlocked($courseId, $userId)
    {
        $redis = $this->getRedis();

        $key = $this->getLiveLockCacheKey($courseId);

        return $redis->sIsMember($key, $userId);
    }

    public function addBlockCache($courseId, $userId)
    {
        $redis = $this->getRedis();

        $key = $this->getLiveLockCacheKey($courseId);

        if (is_array($userId)) {
            $redis->sAdd($key, ...$userId);
        } else {
            $redis->sAdd($key, $userId);
        }
    }

    public function removeBlockCache($courseId, $userId)
    {
        $redis = $this->getRedis();

        $key = $this->getLiveLockCacheKey($courseId);

        if (is_array($userId)) {
            $redis->sRem($key, ...$userId);
        } else {
            $redis->sRem($key, $userId);
        }
    }

    public function deleteLiveBlockCache($courseId)
    {
        $redis = $this->getRedis();

        $key = $this->getLiveLockCacheKey($courseId);

        $redis->del($key);
    }

    protected function rebuildChapterLiveCache($chapterId)
    {
        $cache = new ChapterLiveCache();

        $cache->rebuild($chapterId);
    }

    protected function getLiveLockCacheKey($courseId)
    {
        return "live_block:{$courseId}";
    }

    protected function handleBlockedUsers($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new LiveBlockListBuilder();

        $relations = $pager->items->toArray();

        $users = $builder->getUsers($relations);

        $items = [];

        foreach ($relations as $relation) {

            $user = $users[$relation['user_id']] ?? new \stdClass();

            $items[] = [
                'id' => $relation['id'],
                'expire_time' => $relation['expire_time'],
                'create_time' => $relation['create_time'],
                'update_time' => $relation['update_time'],
                'user' => $user,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
