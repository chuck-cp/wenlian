<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\Certificate as CertificateModel;
use App\Models\CertificateUser as CertificateUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class Certificate extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(CertificateModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['name'])) {
            $builder->andWhere('name LIKE :name:', ['name' => "%{$where['name']}%"]);
        }

        if (!empty($where['grant_type'])) {
            if (is_array($where['grant_type'])) {
                $builder->inWhere('grant_type', $where['grant_type']);
            } else {
                $builder->andWhere('grant_type = :grant_type:', ['grant_type' => $where['grant_type']]);
            }
        }

        if (!empty($where['item_type'])) {
            if (is_array($where['item_type'])) {
                $builder->inWhere('item_type', $where['item_type']);
            } else {
                $builder->andWhere('item_type = :item_type:', ['item_type' => $where['item_type']]);
            }
        }

        if (!empty($where['create_time'][0]) && !empty($where['create_time'][1])) {
            $startTime = strtotime($where['create_time'][0]);
            $endTime = strtotime($where['create_time'][1]);
            $builder->betweenWhere('create_time', $startTime, $endTime);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'popular':
                $orderBy = 'grant_count DESC';
                break;
            case 'oldest':
                $orderBy = 'id ASC';
                break;
            default:
                $orderBy = 'id DESC';
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
     * @return CertificateModel|Model|bool
     */
    public function findById($id)
    {
        return CertificateModel::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * @param int $itemId
     * @param int $itemType
     * @return CertificateModel|Model|bool
     */
    public function findItemCertificate($itemId, $itemType)
    {
        return CertificateModel::findFirst([
            'conditions' => 'item_id = :item_id: AND item_type = :item_type: AND deleted = 0',
            'bind' => ['item_id' => $itemId, 'item_type' => $itemType],
            'order' => 'id DESC',
        ]);
    }

    /**
     * @param array $ids
     * @param string|array $columns
     * @return ResultsetInterface|Resultset|CertificateModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return CertificateModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

    public function countGrants($certId)
    {
        return (int)CertificateUserModel::count([
            'conditions' => 'cert_id = :cert_id: AND deleted = 0',
            'bind' => ['cert_id' => $certId],
        ]);
    }

}
