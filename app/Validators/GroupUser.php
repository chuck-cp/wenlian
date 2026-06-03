<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\GroupUser as GroupUserRepo;

class GroupUser extends Validator
{

    public function checkById($id)
    {
        $repo = new GroupUserRepo();

        $groupUser = $repo->findById($id);

        if (!$groupUser) {
            throw new BadRequestException('group_user.not_found');
        }

        return $groupUser;
    }

    public function checkGroup($id)
    {
        $validator = new Group();

        return $validator->checkGroup($id);
    }

    public function checkUser($name)
    {
        $validator = new Account();

        $account = $validator->checkAccount($name);

        $validator = new User();

        return $validator->checkUser($account->id);
    }

}
