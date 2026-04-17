<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ExamPaperUserList as ExamPaperUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Validators\ExamPaper as ExamPaperValidator;
use Vtiful\Kernel\Excel;

class ExamPaperUserExport extends Service
{

    use AccountSearchTrait;

    public function handle($id)
    {
        $paper = $this->findExamPaperOrFail($id);

        $pager = $this->searchExamPaperUsers($paper);

        if ($pager->total_items == 0) {
            return null;
        }

        set_time_limit(300);

        ini_set('memory_limit', '512M');

        $header = [
            0 => '用户编号',
            1 => '用户昵称',
            2 => '来源类型',
            3 => '加入时间',
            4 => '过期时间',
        ];

        $rows = [];

        foreach ($pager->items as $item) {
            $rows[] = [
                0 => $item['user']['id'],
                1 => $item['user']['name'],
                2 => $this->getSourceTypeText($item['source_type']),
                3 => date('Y-m-d H:i:s', $item['create_time']),
                4 => $this->getExpiryTimeText($item['expiry_time']),
            ];
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('试卷-%s-学员列表-%s.xlsx', $paper->title, date('Ymd'));

        $filePath = $excel->fileName($filename)->header($header)->data($rows)->output();

        kg_download($filePath);
    }

    protected function searchExamPaperUsers(ExamPaperModel $paper)
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['paper_id'] = $paper->id;
        $params['debut'] = 1;
        $params['deleted'] = 0;

        $repo = new ExamPaperUserRepo();

        $pager = $repo->paginate($params, 'latest', 1, 10000);

        if ($pager->total_items > 0) {
            $builder = new ExamPaperUserListBuilder();
            $items = $pager->items->toArray();
            $pager->items = $builder->handleUsers($items);
        }

        return $pager;
    }

    protected function getSourceTypeText($type)
    {
        $list = ExamPaperUserModel::sourceTypes();

        return $list[$type] ?? 'N/A';
    }

    protected function getExpiryTimeText($time)
    {
        return $time > 0 ? date('Y-m-d H:i:s', $time) : 'N/A';
    }

    protected function findExamPaperOrFail($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaper($id);
    }

}
