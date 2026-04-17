<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Validators\Common as CommonValidator;
use App\Models\Course as CourseModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\Account as AccountRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Course\CourseUserTrait;
use App\Validators\Course as CourseValidator;
use Vtiful\Kernel\Excel;

class CourseUserImport extends Service
{

    use CourseUserTrait;

    public function handle($id)
    {
        $course = $this->findCourseOrFail($id);

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
                Excel::TYPE_TIMESTAMP,
            ])
            ->getSheetData();

        if (count($rows) < 3) return;

        foreach ($rows as $key => $value) {
            if ($key > 1) {
                $this->handleRow($course, $value);
            }
        }

        $this->recountCourseUsers($course);
    }

    protected function handleRow(CourseModel $course, array $row)
    {
        $name = $this->filter->sanitize($row[0], ['trim', 'string']);
        $expiryTime = $this->filter->sanitize($row[1], ['trim', 'int']);

        if (empty($name)) return;

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

        $userRepo = new UserRepo();

        $user = $userRepo->findById($account->id);

        if ($expiryTime < time()) {
            $expiryTime = strtotime('+12 months');
        }

        $sourceType = KgOwnershipModel::SOURCE_MANUAL;

        try {

            $this->db->begin();

            $this->assignUserCourse($course, $user, $expiryTime, $sourceType);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Import Course User Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'row' => $row,
                ]));
        }
    }

    protected function findCourseOrFail($id)
    {
        $validator = new CourseValidator();

        return $validator->checkCourse($id);
    }

}
