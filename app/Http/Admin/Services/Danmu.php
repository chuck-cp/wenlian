<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\DanmuList as DanmuListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Danmu as DanmuModel;
use App\Models\Reason as ReasonModel;
use App\Models\User as UserModel;
use App\Repos\Danmu as DanmuRepo;
use App\Services\Logic\Danmu\DanmuInfo as DanmuInfoService;
use App\Validators\Danmu as DanmuValidator;

class Danmu extends Service
{

    public function getReasons()
    {
        return ReasonModel::commentRejectOptions();
    }

    public function getDanmus()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $danmuRepo = new DanmuRepo();

        $pager = $danmuRepo->paginate($params, $sort, $page, $limit);

        return $this->handleDanmus($pager);
    }

    public function getDanmu($id)
    {
        return $this->findOrFail($id);
    }

    public function getDanmuInfo($id)
    {
        $service = new DanmuInfoService();

        return $service->handle($id);
    }

    public function updateDanmu($id)
    {
        $danmu = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new DanmuValidator();

        $data = [];

        if (isset($post['text'])) {
            $data['text'] = $validator->checkText($post['text']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        $danmu->assign($data);

        $danmu->update();

        $this->eventsManager->fire('Danmu:afterUpdate', $this, $danmu);

        return $danmu;
    }

    public function deleteDanmu($id)
    {
        $danmu = $this->findOrFail($id);

        $danmu->deleted = 1;

        $danmu->update();

        $sender = $this->getLoginUser();

        $this->handleDanmuDeletedNotice($danmu, $sender);

        $this->eventsManager->fire('Danmu:afterDelete', $this, $danmu);

        return $danmu;
    }

    public function restoreDanmu($id)
    {
        $danmu = $this->findOrFail($id);

        $danmu->deleted = 0;

        $danmu->update();

        $this->eventsManager->fire('Danmu:afterRestore', $this, $danmu);

        return $danmu;
    }

    public function moderate($id)
    {
        $type = $this->request->getPost('type', ['trim', 'string']);
        $reason = $this->request->getPost('reason', ['trim', 'string']);

        $danmu = $this->findOrFail($id);

        $sender = $this->getLoginUser();

        if ($type == 'approve') {

            $danmu->published = DanmuModel::PUBLISH_APPROVED;
            $danmu->update();

            $this->handleDanmuApprovedNotice($danmu, $sender);

            $this->eventsManager->fire('Danmu:afterApprove', $this, $danmu);

        } elseif ($type == 'reject') {

            $danmu->published = DanmuModel::PUBLISH_REJECTED;
            $danmu->update();

            $this->handleDanmuRejectedNotice($danmu, $sender, $reason);

            $this->eventsManager->fire('Danmu:afterReject', $this, $danmu);
        }

        return $danmu;
    }

    public function batchModerate()
    {
        $type = $this->request->getQuery('type', ['trim', 'string']);
        $ids = $this->request->getPost('ids', ['trim', 'int']);

        $danmuRepo = new DanmuRepo();

        $danmus = $danmuRepo->findByIds($ids);

        if ($danmus->count() == 0) return;

        $sender = $this->getLoginUser();

        foreach ($danmus as $danmu) {

            if ($type == 'approve') {

                $danmu->published = DanmuModel::PUBLISH_APPROVED;
                $danmu->update();

                $this->handleDanmuApprovedNotice($danmu, $sender);

            } elseif ($type == 'reject') {

                $danmu->published = DanmuModel::PUBLISH_REJECTED;
                $danmu->update();

                $this->handleDanmuRejectedNotice($danmu, $sender);
            }
        }
    }

    public function batchDelete()
    {
        $ids = $this->request->getPost('ids', ['trim', 'int']);

        $danmuRepo = new DanmuRepo();

        $danmus = $danmuRepo->findByIds($ids);

        if ($danmus->count() == 0) return;

        $sender = $this->getLoginUser();

        foreach ($danmus as $danmu) {
            $danmu->deleted = 1;
            $danmu->update();
            $this->handleDanmuDeletedNotice($danmu, $sender);
        }
    }

    protected function findOrFail($id)
    {
        $validator = new DanmuValidator();

        return $validator->checkDanmu($id);
    }

    protected function handleDanmuApprovedNotice(DanmuModel $danmu, UserModel $sender)
    {

    }

    protected function handleDanmuRejectedNotice(DanmuModel $danmu, UserModel $sender, $reason = '')
    {

    }

    protected function handleDanmuDeletedNotice(DanmuModel $danmu, UserModel $sender, $reason = '')
    {

    }

    protected function handleDanmus($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new DanmuListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleUsers($pipeA);
            $pipeC = $builder->objects($pipeB);

            $pager->items = $pipeC;
        }

        return $pager;
    }

}
