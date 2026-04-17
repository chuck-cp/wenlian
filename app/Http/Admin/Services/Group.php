<?php
/**
 * @copyright Copyright (c) 2025 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\Group as GroupModel;
use App\Repos\Group as GroupRepo;
use App\Validators\Group as GroupValidator;

class Group extends Service
{

    public function getGroups()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $groupRepo = new GroupRepo();

        return $groupRepo->paginate($params, $sort, $page, $limit);
    }

    public function getGroup($id)
    {
        return $this->findOrFail($id);
    }

    public function createGroup()
    {
        $post = $this->request->getPost();

        $validator = new GroupValidator();

        $group = new GroupModel();

        $group->name = $validator->checkName($post['name']);

        $group->create();

        return $group;
    }

    public function updateGroup($id)
    {
        $group = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new GroupValidator();

        $data = [];

        if (isset($post['name'])) {
            $data['name'] = $validator->checkName($post['name']);
            if (strtolower($data['name']) != strtolower($group->name)) {
                $validator->checkIfNameExists($data['name']);
            }
        }

        if (isset($post['expiry_time'])) {
            $data['expiry_time'] = $validator->checkExpiryTime($post['expiry_time']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        $group->assign($data);

        $group->update();

        return $group;
    }

    public function deleteGroup($id)
    {
        $group = $this->findOrFail($id);

        $group->deleted = 1;

        $group->update();

        return $group;
    }

    public function restoreGroup($id)
    {
        $group = $this->findOrFail($id);

        $group->deleted = 0;

        $group->update();

        return $group;
    }

    protected function findOrFail($id)
    {
        $validator = new GroupValidator();

        return $validator->checkGroup($id);
    }

}
