<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Utils\Password as PasswordUtil;
use App\Library\Validators\Common as CommonValidator;
use App\Models\Account as AccountModel;
use App\Repos\Account as AccountRepo;
use App\Repos\User as UserRepo;
use Vtiful\Kernel\Excel;

class UserImport extends Service
{

    public function handle()
    {
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
                Excel::TYPE_STRING,
                Excel::TYPE_STRING,
                Excel::TYPE_TIMESTAMP,
                Excel::TYPE_INT,
            ])
            ->getSheetData();

        $importUserCount = count($rows) - 2;

        if ($importUserCount > 5000) {
            throw new BadRequestException('user_import.too_many_rows');
        }

        $licenseInfo = $this->getMyLicenseInfo();

        if ($licenseInfo['auth_type'] == 'user_count') {
            $userCount = $this->getUserCount();
            if ($userCount + $importUserCount > $licenseInfo['user_count']) {
                throw new BadRequestException('user_import.exceed_licensed_user_count');
            }
        }

        if ($importUserCount < 1) return;

        foreach ($rows as $key => $value) {
            if ($key > 1) {
                $this->handleRow($value);
            }
        }
    }

    protected function handleRow($row)
    {
        $phone = $this->filter->sanitize($row[0], ['trim', 'string']);
        $password = $this->filter->sanitize($row[1], ['trim', 'string']);
        $name = $this->filter->sanitize($row[2], ['trim', 'string']);
        $vipExpiryTime = $this->filter->sanitize($row[3], ['trim', 'int']);
        $point = $this->filter->sanitize($row[4], ['trim', 'int']);

        if (!CommonValidator::phone($phone)) return;

        if (!CommonValidator::password($password)) return;

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findByPhone($phone);

        if ($account) return;

        try {

            $this->db->begin();

            $account = new AccountModel();

            $salt = PasswordUtil::salt();
            $password = PasswordUtil::hash($password, $salt);

            $account->phone = $phone;
            $account->salt = $salt;
            $account->password = $password;
            $account->create();

            $userRepo = new UserRepo();

            $user = $userRepo->findById($account->id);

            if (CommonValidator::nickname($name)) {
                $user->name = $name;
            }

            if ($vipExpiryTime > time()) {
                $user->vip_expiry_time = $vipExpiryTime;
                $user->vip = 1;
            }

            $user->update();

            if ($point > 0 && $point < 10000) {
                $balance = $userRepo->findUserBalance($user->id);
                $balance->point = $point;
                $balance->update();
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Import User Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'row' => $row,
                ]));
        }
    }

    protected function getUserCount()
    {
        $userRepo = new UserRepo();

        return $userRepo->countUsers();
    }

}
