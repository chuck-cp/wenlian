<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Repos\Certificate as CertificateRepo;

class CertificateUserList extends Builder
{

    public function handleCerts($relations)
    {
        $certificates = $this->getCerts($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['cert'] = $certificates[$value['cert_id']] ?? null;
        }

        return $relations;
    }

    public function handleUsers($relations)
    {
        $users = $this->getUsers($relations);

        foreach ($relations as $key => $value) {
            $relations[$key]['user'] = $users[$value['user_id']] ?? null;
        }

        return $relations;
    }

    public function getCerts($relations)
    {
        $ids = kg_array_column($relations, 'cert_id');

        $certRepo = new CertificateRepo();

        $columns = [
            'id', 'name', 'grant_type', 'item_id', 'item_type', 'item_info',
            'attrs', 'published', 'deleted', 'grant_count',
        ];

        $certificates = $certRepo->findByIds($ids, $columns);

        $result = [];

        foreach ($certificates->toArray() as $cert) {
            $cert['item_info'] = json_decode($cert['item_info'], true);
            $cert['attrs'] = json_decode($cert['attrs'], true);
            $result[$cert['id']] = $cert;
        }

        return $result;
    }

    public function getUsers($relations)
    {
        $ids = kg_array_column($relations, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
