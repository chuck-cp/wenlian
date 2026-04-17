<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\CourseUserList as CourseUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Course as CourseModel;
use App\Models\CourseUser as CourseUserModel;
use App\Repos\CourseUser as CourseUserRepo;
use App\Validators\Course as CourseValidator;
use Vtiful\Kernel\Excel;

class CourseUserExport extends Service
{

    use AccountSearchTrait;

    public function handle($id)
    {
        $course = $this->findCourseOrFail($id);

        $pager = $this->searchCourseUsers($course);

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
            5 => '学习进度',
            6 => '学习时长',
            7 => '最近学习',
        ];

        $rows = [];

        foreach ($pager->items as $item) {
            $rows[] = [
                0 => $item['user']['id'],
                1 => $item['user']['name'],
                2 => $this->getSourceTypeText($item['source_type']),
                3 => date('Y-m-d H:i:s', $item['create_time']),
                4 => $this->getExpiryTimeText($item['expiry_time']),
                5 => $this->getProgressText($item['progress']),
                6 => $this->getDurationText($item['duration']),
                7 => $this->getActiveTimeText($item['active_time']),
            ];
        }

        $excel = new Excel(['path' => tmp_path()]);

        $filename = sprintf('课程-%s-学员列表-%s.xlsx', $course->title, date('Ymd'));

        $filePath = $excel->fileName($filename)->header($header)->data($rows)->output();

        kg_download($filePath);
    }

    protected function searchCourseUsers(CourseModel $course)
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['course_id'] = $course->id;
        $params['deleted'] = 0;

        $repo = new CourseUserRepo();

        $pager = $repo->paginate($params, 'latest', 1, 10000);

        if ($pager->total_items > 0) {
            $builder = new CourseUserListBuilder();
            $items = $pager->items->toArray();
            $pager->items = $builder->handleUsers($items);
        }

        return $pager;
    }

    protected function getSourceTypeText($type)
    {
        $list = CourseUserModel::sourceTypes();

        return $list[$type] ?? 'N/A';
    }

    protected function getProgressText($progress)
    {
        return $progress > 0 ? sprintf('%s%%', $progress) : 'N/A';
    }

    protected function getDurationText($duration)
    {
        return $duration > 0 ? kg_duration($duration) : 'N/A';
    }

    protected function getExpiryTimeText($time)
    {
        return $time > 0 ? date('Y-m-d H:i:s', $time) : 'N/A';
    }

    protected function getActiveTimeText($time)
    {
        return $time > 0 ? date('Y-m-d H:i:s', $time) : 'N/A';
    }

    protected function findCourseOrFail($id)
    {
        $validator = new CourseValidator();

        return $validator->checkCourse($id);
    }

}
