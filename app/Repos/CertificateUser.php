<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\CertificateUser as CertificateUserModel;
use Phalcon\Mvc\Model;

class CertificateUser extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(CertificateUserModel::class);

        $builder->where('1 = 1');

        if (!empty($where['sn'])) {
            $builder->andWhere('sn = :sn:', ['sn' => $where['sn']]);
        }

        if (!empty($where['cert_id'])) {
            $builder->andWhere('cert_id = :cert_id:', ['cert_id' => $where['cert_id']]);
        }

        if (!empty($where['user_id'])) {
            $builder->andWhere('user_id = :user_id:', ['user_id' => $where['user_id']]);
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
     * @return CertificateUserModel|Model|bool
     */
    public function findById($id)
    {
        return CertificateUserModel::findFirst($id);
    }

    /**
     * @param string $sn
     * @return CertificateUserModel|Model|bool
     */
    public function findBySn($sn)
    {
        return CertificateUserModel::findFirst([
            'conditions' => 'sn = :sn:',
            'bind' => ['sn' => $sn],
        ]);
    }

    /**
     * @param int $certId
     * @param int $userId
     * @return CertificateUserModel|Model|bool
     */
    public function findCertUser($certId, $userId)
    {
        return CertificateUserModel::findFirst([
            'conditions' => 'cert_id = ?1 AND user_id = ?2 AND deleted = 0',
            'bind' => [1 => $certId, 2 => $userId],
            'order' => 'id DESC',
        ]);
    }

}
