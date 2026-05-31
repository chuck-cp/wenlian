<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Models\User as UserModel;
use App\Repos\Account as AccountRepo;
use App\Repos\Role as RoleRepo;

class UserList extends Builder
{

    public function handleUsers(array $users)
    {
        $baseUrl = kg_cos_url();

        foreach ($users as $key => $user) {
            $users[$key]['avatar'] = $baseUrl . $user['avatar'];
        }

        return $users;
    }

    public function handleAccounts(array $users)
    {
        $accounts = $this->getAccounts($users);

        foreach ($users as $key => $user) {
            $users[$key]['account'] = $accounts[$user['id']] ?? null;
        }

        return $users;
    }

    public function handleAdminRoles(array $users)
    {
        $roles = $this->getAdminRoles($users);

        foreach ($users as $key => $user) {
            $users[$key]['admin_role'] = $roles[$user['admin_role']] ?? ['id' => 0, 'name' => 'N/A'];
        }

        return $users;
    }

    public function handleEduRoles(array $users)
    {
        foreach ($users as $key => $user) {
            $eduRole = $user['edu_role'] ?? 0;
            $users[$key]['edu_role'] = [
                'id' => $eduRole,
                'name' => UserModel::formatEduRoleName($eduRole, $user['edu_role_label'] ?? ''),
            ];
        }

        return $users;
    }

    protected function getAccounts(array $users)
    {
        $ids = kg_array_column($users, 'id');

        $accountRepo = new AccountRepo();

        $accounts = $accountRepo->findByIds($ids);

        $result = [];

        foreach ($accounts as $account) {
            $result[$account->id] = [
                'phone' => $account->phone,
                'email' => $account->email,
            ];
        }

        return $result;
    }

    protected function getAdminRoles(array $users)
    {
        $ids = kg_array_column($users, 'admin_role');

        $roleRepo = new RoleRepo();

        $roles = $roleRepo->findByIds($ids, ['id', 'name']);

        $result = [];

        foreach ($roles->toArray() as $role) {
            $result[$role['id']] = $role;
        }

        return $result;
    }

}
