<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Validators\Common as CommonValidator;
use App\Models\Group as GroupModel;
use App\Models\GroupUser as GroupUserModel;
use App\Repos\Account as AccountRepo;
use App\Repos\Group as GroupRepo;
use App\Repos\GroupUser as GroupUserRepo;
use App\Validators\Group as GroupValidator;
use Vtiful\Kernel\Excel;

class GroupUserImport extends Service
{

    public function handle($id)
    {
        $group = $this->findGroupOrFail($id);

        set_time_limit(300);

        ini_set('memory_limit', '512M');

        $path = $this->request->getPost('path');

        $dirname = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_BASENAME);

        $excel = new Excel(['path' => $dirname]);

        $rows = $excel
            ->openFile($filename)
            ->openSheet(null, Excel::SKIP_EMPTY_ROW)
            ->setType([
                Excel::TYPE_STRING,
            ])
            ->getSheetData();

        if (count($rows) < 3) return;

        foreach ($rows as $key => $value) {
            if ($key > 1) {
                $this->handleRow($group, $value);
            }
        }

        $this->recountGroupUsers($group);
    }

    protected function handleRow(GroupModel $group, array $row)
    {
        $name = $this->filter->sanitize($row[0], ['trim', 'string']);

        if (!$name) return;

        $accountRepo = new AccountRepo();

        $account = null;

        if (CommonValidator::email($name)) {
            $account = $accountRepo->findByEmail($name);
        } elseif (CommonValidator::phone($name)) {
            $account = $accountRepo->findByPhone($name);
        } elseif (CommonValidator::intNumber($name)) {
            $account = $accountRepo->findById($name);
        }

        if (!$account) return;

        $groupUserRepo = new GroupUserRepo();

        $groupUser = $groupUserRepo->findGroupUser($group->id, $account->id);

        if ($groupUser) return;

        $groupUserModel = new GroupUserModel();

        $groupUserModel->group_id = $group->id;
        $groupUserModel->user_id = $account->id;
        $groupUserModel->create();
    }

    protected function recountGroupUsers(GroupModel $group)
    {
        $groupRepo = new GroupRepo();

        $userCount = $groupRepo->countUsers($group->id);

        $group->user_count = $userCount;

        $group->update();
    }

    protected function findGroupOrFail($id)
    {
        $validator = new GroupValidator();

        return $validator->checkGroup($id);
    }

}
