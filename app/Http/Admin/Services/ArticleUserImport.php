<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Validators\Common as CommonValidator;
use App\Models\Article as ArticleModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\Account as AccountRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Article\ArticleUserTrait;
use App\Validators\Article as ArticleValidator;
use Vtiful\Kernel\Excel;

class ArticleUserImport extends Service
{

    use ArticleUserTrait;

    public function handle($id)
    {
        $article = $this->findArticleOrFail($id);

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
                $this->handleRow($article, $value);
            }
        }

        $this->recountArticleUsers($article);
    }

    protected function handleRow(ArticleModel $article, array $row)
    {
        $name = $this->filter->sanitize($row[0], ['trim', 'string']);
        $expiryTime = $this->filter->sanitize($row[1], ['trim', 'string']);

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

            $this->assignUserArticle($article, $user, $expiryTime, $sourceType);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Import Article User Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'row' => $row,
                ]));
        }
    }

    protected function findArticleOrFail($id)
    {
        $validator = new ArticleValidator();

        return $validator->checkArticle($id);
    }

}
