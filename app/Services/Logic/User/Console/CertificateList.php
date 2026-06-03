<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Builders\CertificateUserList as CertificateUserListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\CertificateUser as CertificateUserRepo;
use App\Services\Logic\Service as LogicService;

class CertificateList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new CertificateUserRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handlePager($pager);
    }

    protected function handlePager($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new CertificateUserListBuilder();

        $relations = $pager->items->toArray();

        $certs = $builder->getCerts($relations);

        $items = [];

        foreach ($relations as $relation) {

            $cert = $certs[$relation['cert_id']] ?? new \stdClass();

            $items[] = [
                'id' => $relation['id'],
                'sn' => $relation['sn'],
                'create_time' => $relation['create_time'],
                'update_time' => $relation['create_time'],
                'cert' => $cert,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
