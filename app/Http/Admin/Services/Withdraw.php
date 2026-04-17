<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\WithdrawList as WithdrawListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Http\Admin\Services\Traits\WithdrawSearchTrait;
use App\Library\Paginator\Query as PaginateQuery;
use App\Models\Task as TaskModel;
use App\Models\Withdraw as WithdrawModel;
use App\Repos\Account as AccountRepo;
use App\Repos\User as UserRepo;
use App\Repos\Withdraw as WithdrawRepo;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Validators\Withdraw as WithdrawValidator;

class Withdraw extends Service
{

    use AccountSearchTrait;
    use WithdrawSearchTrait;

    public function getStatusTypes()
    {
        return WithdrawModel::statusTypes();
    }

    public function getWithdraws()
    {
        $pageQuery = new PaginateQuery();

        $params = $pageQuery->getParams();

        $params = $this->handleAccountSearchParams($params);
        $params = $this->handleWithdrawSearchParams($params);

        if (!empty($params['withdraw_id'])) {
            $params['id'] = $params['withdraw_id'];
        }

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pageQuery->getSort();
        $page = $pageQuery->getPage();
        $limit = $pageQuery->getLimit();

        $withdrawRepo = new WithdrawRepo();

        $pager = $withdrawRepo->paginate($params, $sort, $page, $limit);

        return $this->handleWithdraws($pager);
    }

    public function getWithdraw($id)
    {
        return $this->findOrFail($id);
    }

    public function getStatusHistory($id)
    {
        $withdrawRepo = new WithdrawRepo();

        return $withdrawRepo->findStatusHistory($id);
    }

    public function getWithdrawAccount($id)
    {
        $accountRepo = new WithdrawAccountRepo();

        return $accountRepo->findById($id);
    }

    public function getUser($id)
    {
        $userRepo = new UserRepo();

        return $userRepo->findById($id);
    }

    public function getAccount($id)
    {
        $accountRepo = new AccountRepo();

        return $accountRepo->findById($id);
    }

    public function reviewWithdraw($id)
    {
        $withdraw = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new WithdrawValidator();

        $validator->checkIfAllowReview($withdraw);

        $withdraw->status = $validator->checkReviewStatus($post['review_status']);
        $withdraw->review_note = $validator->checkReviewNote($post['review_note']);

        try {

            $this->db->begin();

            $task = new TaskModel();

            $itemInfo = [
                'withdraw' => ['id' => $withdraw->id],
            ];

            $task->item_id = $withdraw->id;
            $task->item_info = $itemInfo;
            $task->priority = TaskModel::PRIORITY_HIGH;
            $task->status = TaskModel::STATUS_PENDING;

            if ($withdraw->status == WithdrawModel::STATUS_APPROVED) {
                $task->item_type = TaskModel::TYPE_WITHDRAW_SETTLE;
            } else {
                $task->item_type = TaskModel::TYPE_WITHDRAW_REFUND;
            }

            $task->create();

            $withdraw->update();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('withdraw');

            $logger->error('Withdraw Review Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'withdraw' => $withdraw,
                ]));

            throw new \RuntimeException('sys.rollback');
        }

        if ($withdraw->status == WithdrawModel::STATUS_APPROVED) {
            $this->handleWithdrawApprovedNotice($withdraw);
        } else {
            $this->handleWithdrawRefuseNotice($withdraw);
        }

        return $withdraw;
    }

    protected function handleWithdrawApprovedNotice(WithdrawModel $withdraw)
    {
        /**
         * @todo 提现审批通知
         */
    }

    protected function handleWithdrawRefuseNotice(WithdrawModel $withdraw)
    {
        /**
         * @todo 提现拒绝通知
         */
    }

    protected function findOrFail($id)
    {
        $validator = new WithdrawValidator();

        return $validator->checkById($id);
    }

    protected function handleWithdraws($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new WithdrawListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleUsers($pipeA);
            $pipeC = $builder->handleAccounts($pipeB);
            $pipeD = $builder->objects($pipeC);

            $pager->items = $pipeD;
        }

        return $pager;
    }

}
