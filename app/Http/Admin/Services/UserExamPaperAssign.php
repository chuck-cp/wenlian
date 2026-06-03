<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Services\Logic\Exam\Paper\PaperUserTrait;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class UserExamPaperAssign extends Service
{

    use PaperUserTrait;

    public function getXmExamPapers()
    {
        $paperRepo = new ExamPaperRepo();

        $where = [
            'published' => 1,
            'deleted' => 0,
            'free' => 0,
        ];

        $items = $paperRepo->findAll($where);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function assignExamPaper($id)
    {
        $post = $this->request->getPost();

        $validator = new ExamPaperUserValidator();

        $user = $validator->checkUser($id);

        $expiryTime = $validator->checkExpiryTime($post['expiry_time']);

        $paperIds = $post['xm_paper_ids'] ? explode(',', $post['xm_paper_ids']) : [];

        if (empty($paperIds)) return;

        $paperRepo = new ExamPaperRepo();

        $papers = $paperRepo->findByIds($paperIds);

        $sourceType = KgOwnershipModel::SOURCE_MANUAL;

        foreach ($papers as $paper) {
            $this->assignUserPaper($paper, $user, $expiryTime, $sourceType);
        }
    }

}
