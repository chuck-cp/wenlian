<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\UserList as UserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\User as UserRepo;
use Vtiful\Kernel\Excel;

class UserExport extends Service
{

    use AccountSearchTrait;

    public function handle()
    {
        set_time_limit(300);

        ini_set('memory_limit', '512M');

        return $this->exportUsers();
    }

    protected function exportUsers()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        if (!empty($params['user_id'])) {
            $params['id'] = $params['user_id'];
        }

        $params['deleted'] = $params['deleted'] ?? 0;

        $userRepo = new UserRepo();

        $pager = $userRepo->paginate($params);

        if ($pager->total_items == 0) {
            return null;
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('用户列表-%s.xlsx', date('Ymd'));

        $excel = $excel->fileName($filename);

        $limit = 10000;

        $totalPage = ceil($pager->total_items / $limit);

        $builder = new UserListBuilder();

        for ($page = 1; $page <= $totalPage; $page++) {

            $myPager = $userRepo->paginate($params, 'latest', $page, $limit);

            $items = $myPager->items->toArray();

            $items = $builder->handleAccounts($items);

            $data = [];

            foreach ($items as $item) {
                $data[] = $this->handleExcelRow($item);
            }

            $header = $this->getExcelHeader();

            $excel = $excel->header($header);

            if ($page > 1) {
                $excel = $excel->addSheet("Sheet{$page}");
            }

            $excel = $excel->data($data);
        }

        $filePath = $excel->output();

        kg_download($filePath);
    }

    protected function getExcelHeader()
    {
        return [
            0 => '编号',
            1 => '昵称',
            2 => '手机',
            3 => '邮箱',
            4 => '教学角色',
            5 => '是否会员',
            6 => '注册日期',
            7 => '活跃日期',
        ];
    }

    protected function handleExcelRow($item)
    {
        return [
            0 => $item['id'],
            1 => $item['name'],
            2 => $item['account']['phone'] ?: 'N/A',
            3 => $item['account']['email'] ?: 'N/A',
            4 => $item['edu_role'] == 1 ? '学员' : '讲师',
            5 => $item['vip'] == 1 ? '是' : '否',
            6 => $item['create_time'] > 0 ? date('Y-m-d', $item['create_time']) : 'N/A',
            7 => $item['active_time'] > 0 ? date('Y-m-d', $item['active_time']) : 'N/A',
        ];
    }

}
