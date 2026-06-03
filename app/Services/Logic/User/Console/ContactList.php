<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\User\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\UserContact as UserContactModel;
use App\Repos\UserContact as UserContactRepo;
use App\Services\Logic\Service as LogicService;

class ContactList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser(true);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $repo = new UserContactRepo();

        $pager = $repo->paginate($params, $sort, $page, $limit);

        return $this->handleContacts($pager);
    }

    public function handleContacts($pager)
    {
        $items = [];

        if ($pager->total_items == 0) {
            return $items;
        }

        /**
         * @var $contacts UserContactModel[]
         */
        $contacts = $pager->items;

        foreach ($contacts as $contact) {
            $items[] = [
                'id' => $contact->id,
                'name' => $contact->name,
                'phone' => $contact->phone,
                'add_province' => $contact->add_province,
                'add_city' => $contact->add_city,
                'add_county' => $contact->add_county,
                'add_other' => $contact->add_other,
                'master' => $contact->master,
                'create_time' => $contact->create_time,
                'update_time' => $contact->update_time,
            ];
        }

        return $items;
    }

}
