<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Validators\Common as CommonValidator;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\Account as AccountRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Exam\Paper\PaperUserTrait;
use App\Validators\ExamPaper as ExamPaperValidator;
use Vtiful\Kernel\Excel;

class ExamPaperUserImport extends Service
{

    use PaperUserTrait;

    public function handle($id)
    {
        $paper = $this->findExamPaperOrFail($id);

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
                $this->handleRow($paper, $value);
            }
        }

        $this->recountPaperJoins($paper);
    }

    protected function handleRow(ExamPaperModel $paper, array $row)
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

            $this->assignUserPaper($paper, $user, $expiryTime, $sourceType);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Import Exam Paper User Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'row' => $row,
                ]));
        }
    }

    protected function findExamPaperOrFail($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaper($id);
    }

}
