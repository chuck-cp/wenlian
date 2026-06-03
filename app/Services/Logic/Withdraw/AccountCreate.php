<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Withdraw;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\KgProduct as KgProductModel;
use App\Models\Order as OrderModel;
use App\Models\User as UserModel;
use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Repos\Order as OrderRepo;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Services\Logic\Service as LogicService;
use App\Traits\Client as ClientTrait;
use App\Validators\WithdrawAccount as WithdrawAccountValidator;

class AccountCreate extends LogicService
{

    use ClientTrait;

    public function handle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        try {

            $this->db->begin();

            $account = $this->createAccount($user, $post);

            $order = $this->createOrder($user, $account);

            $account->order_id = $order->id;

            $account->update();

            $this->db->commit();

            return [
                'account' => $account,
                'order' => $order,
            ];

        } catch (BadRequestException $e) {

            $this->db->rollback();

            throw new BadRequestException($e->getMessage());

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('withdraw');

            $logger->error('Create Withdraw Account Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

    protected function createAccount(UserModel $user, $post)
    {
        $validator = new WithdrawAccountValidator();

        $data = [];

        $data['user_id'] = $user->id;
        $data['name'] = $validator->checkName($post['name']);
        $data['channel'] = $validator->checkChannel($post['channel']);
        $data['account'] = $validator->checkAccount($post['account']);

        $accountRepo = new WithdrawAccountRepo();

        $account = $accountRepo->findByUserChannelAccount($user->id, $data['account'], $data['channel']);

        if ($account && $account->verified == 0 && $account->deleted == 0) {
            return $account;
        }

        $account = new WithdrawAccountModel();

        $account->assign($data);

        $account->create();

        return $account;
    }

    protected function createOrder(UserModel $user, WithdrawAccountModel $account)
    {
        $itemType = KgProductModel::ITEM_PAY_ACCOUNT_VERIFY;

        $orderRepo = new OrderRepo();

        $order = $orderRepo->findUserLastPendingOrder($user->id, $account->id, $itemType);

        if ($order) return $order;

        $order = new OrderModel();

        $itemInfo = [
            'withdraw_account' => [
                'id' => $account->id,
                'name' => $account->name,
                'account' => $account->account,
                'channel' => $account->channel,
            ],
        ];

        $order->subject = '验证 - 提现账户验证';
        $order->amount = 0.01;
        $order->owner_id = $user->id;
        $order->item_id = $account->id;
        $order->item_type = $itemType;
        $order->item_info = $itemInfo;
        $order->client_type = $this->getClientType();
        $order->client_ip = $this->getClientIp();

        $order->create();

        return $order;
    }

}
