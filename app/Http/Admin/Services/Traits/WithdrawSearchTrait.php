<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services\Traits;

use App\Repos\Withdraw as WithdrawRepo;

trait WithdrawSearchTrait
{

    protected function handleWithdrawSearchParams($params)
    {
        /**
         * 兼容提现编号或序号查询
         */
        if (!empty($params['withdraw_id']) && strlen($params['withdraw_id']) > 10) {

            $withdrawRepo = new WithdrawRepo();

            $withdraw = $withdrawRepo->findBySn($params['withdraw_id']);

            $params['withdraw_id'] = $withdraw ? $withdraw->id : -1000;
        }

        return $params;
    }

}
