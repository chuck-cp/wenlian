<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Paginator\Query as PaginateQuery;
use App\Repos\Online as OnlineRepo;
use App\Validators\User as UserValidator;

class UserOnline extends Service
{

    public function getOnlines($id)
    {
        $user = $this->findUserOrFail($id);

        $pageQuery = new PaginateQuery();

        $params = $pageQuery->getParams();

        $params['user_id'] = $user->id;

        $sort = $pageQuery->getSort();
        $page = $pageQuery->getPage();
        $limit = $pageQuery->getLimit();

        $onlineRepo = new OnlineRepo();

        return $onlineRepo->paginate($params, $sort, $page, $limit);
    }

    protected function findUserOrFail($id)
    {
        $validator = new UserValidator();

        return $validator->checkUser($id);
    }

}
