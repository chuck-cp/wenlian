<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\GroupUserList as GroupUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Group as GroupModel;
use App\Models\GroupUser as GroupUserModel;
use App\Repos\Group as GroupRepo;
use App\Repos\GroupUser as GroupUserRepo;
use App\Validators\GroupUser as GroupUserValidator;

class GroupUser extends Service
{

    use AccountSearchTrait;

    public function create()
    {
        $post = $this->request->getPost();

        $validator = new GroupUserValidator();

        $group = $validator->checkGroup($post['group_id']);
        $user = $validator->checkUser($post['user_id']);

        $groupUserRepo = new GroupUserRepo();

        $groupUser = $groupUserRepo->findGroupUser($group->id, $user->id);

        if (!$groupUser) {
            $groupUserModel = new GroupUserModel();
            $groupUserModel->group_id = $group->id;
            $groupUserModel->user_id = $user->id;
            $groupUserModel->create();
        }

        $this->recountGroupUsers($group);
    }

    public function delete($id)
    {
        $validator = new GroupUserValidator();

        $groupUser = $validator->checkById($id);

        $group = $validator->checkGroup($groupUser->group_id);

        $groupUser->delete();

        $this->recountGroupUsers($group);
    }

    public function getUsers($id)
    {
        $validator = new GroupUserValidator();

        $group = $validator->checkGroup($id);

        $pagerQuery = new PagerQuery();

        $params['group_id'] = $group->id;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new GroupUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleUsers($pager);
    }

    protected function recountGroupUsers(GroupModel $group)
    {
        $groupRepo = new GroupRepo();

        $userCount = $groupRepo->countUsers($group->id);

        $group->user_count = $userCount;

        $group->update();
    }

    protected function handleUsers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new GroupUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleUsers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
