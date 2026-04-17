<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Invoice as InvoiceModel;
use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Models\InvoiceStatus as InvoiceStatusModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Invoice extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->columns('i.*');
        $builder->addFrom(InvoiceModel::class, 'i');
        $builder->join(InvoiceAccountModel::class, 'i.account_id = ia.id', 'ia');
        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('i.id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('i.user_id = :user_id:', ['user_id' => $where['user_id']]);
        }

        if (!empty($where['sort_no'])) {
            $builder->andWhere('i.sort_no = :sort_no:', ['sort_no' => $where['sort_no']]);
        }

        if (!empty($where['serial_no'])) {
            $builder->andWhere('i.serial_no = :serial_no:', ['serial_no' => $where['serial_no']]);
        }

        if (!empty($where['head_name'])) {
            $builder->andWhere('ia.head_name = :head_name:', ['head_name' => $where['head_name']]);
        }

        if (!empty($where['head_type'])) {
            if (is_array($where['head_type'])) {
                $builder->inWhere('ia.head_type', $where['head_type']);
            } else {
                $builder->andWhere('ia.head_type = :head_type:', ['head_type' => $where['head_type']]);
            }
        }

        if (!empty($where['usage_type'])) {
            if (is_array($where['usage_type'])) {
                $builder->inWhere('ia.usage_type', $where['usage_type']);
            } else {
                $builder->andWhere('ia.usage_type = :usage_type:', ['usage_type' => $where['usage_type']]);
            }
        }

        if (!empty($where['media_type'])) {
            if (is_array($where['media_type'])) {
                $builder->inWhere('i.media_type', $where['media_type']);
            } else {
                $builder->andWhere('i.media_type = :media_type:', ['media_type' => $where['media_type']]);
            }
        }

        if (!empty($where['status'])) {
            if (is_array($where['status'])) {
                $builder->inWhere('i.status', $where['status']);
            } else {
                $builder->andWhere('i.status = :status:', ['status' => $where['status']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('i.deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'oldest':
                $orderBy = 'i.id ASC';
                break;
            default:
                $orderBy = 'i.id DESC';
                break;
        }

        $builder->orderBy($orderBy);

        $pager = new PagerQueryBuilder([
            'builder' => $builder,
            'page' => $page,
            'limit' => $limit,
        ]);

        return $pager->paginate();
    }

    /**
     * @param int $id
     * @return InvoiceModel|Model|bool
     */
    public function findById($id)
    {
        return InvoiceModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $invoiceId
     * @return ResultsetInterface|Resultset|InvoiceStatusModel[]
     */
    public function findStatusHistory($invoiceId)
    {
        return InvoiceStatusModel::query()
            ->where('invoice_id = :invoice_id:', ['invoice_id' => $invoiceId])
            ->execute();
    }

    /**
     * @param int $userId
     * @return int
     */
    public function countUserMonthlyInvoices($userId)
    {
        $status = InvoiceModel::STATUS_FINISHED;

        $time = strtotime(date('Y-m'));

        return (int)InvoiceModel::count([
            'conditions' => 'user_id = ?1 AND status = ?2 AND create_time > ?3',
            'bind' => [1 => $userId, 2 => $status, 3 => $time],
        ]);
    }

}
