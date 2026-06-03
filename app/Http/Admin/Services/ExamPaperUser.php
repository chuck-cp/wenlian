<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ExamPaperUserList as ExamPaperUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Services\Logic\Exam\Paper\PaperUserTrait as ExamPaperUserTrait;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class ExamPaperUser extends Service
{

    use ExamPaperUserTrait;
    use AccountSearchTrait;

    public function getSourceTypes()
    {
        return ExamPaperUserModel::sourceTypes();
    }

    public function create()
    {
        $post = $this->request->getPost();

        $validator = new ExamPaperUserValidator();

        $paper = $validator->checkExamPaper($post['paper_id']);

        $user = $validator->checkUser($post['user_id']);

        $expiryTime = $validator->checkExpiryTime($post['expiry_time']);

        $sourceType = KgOwnershipModel::SOURCE_MANUAL;

        $this->assignUserPaper($paper, $user, $expiryTime, $sourceType);
    }

    public function get($id)
    {
        $validator = new ExamPaperUserValidator();

        return $validator->checkById($id);
    }

    public function update($id)
    {
        $post = $this->request->getPost();

        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        $paperUser->expiry_time = $validator->checkExpiryTime($post['expiry_time']);

        $paperUser->update();
    }

    public function delete($id)
    {
        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        $paperUser->deleted = 1;

        $paperUser->update();

        $paper = $validator->checkExamPaper($paperUser->paper_id);

        $this->recountPaperJoins($paper);
    }

    public function getUsers($id)
    {
        $validator = new ExamPaperUserValidator();

        $paper = $validator->checkExamPaper($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['paper_id'] = $paper->id;
        $params['debut'] = 1;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $paperUserRepo = new ExamPaperUserRepo();

        $pager = $paperUserRepo->paginate($params, $sort, $page, $limit);

        return $this->handleUsers($pager);
    }

    protected function handleUsers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ExamPaperUserListBuilder();

            $items = $pager->items->toArray();
            $pipeA = $builder->handleUsers($items);
            $pipeB = $builder->objects($pipeA);

            $pager->items = $pipeB;
        }

        return $pager;
    }

}
