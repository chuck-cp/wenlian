<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\User as UserModel;

class TeacherGradeExam extends Repository
{

    public function paginate($userId, $page = 1, $limit = 15)
    {
        $columns = [
            'paper_id' => 'ep.id',
            'paper_title' => 'ep.title',
            'user_id' => 'user.id',
            'user_name' => 'user.name',
            'paper_user_id' => 'epu.id',
            'paper_user_create_time' => 'epu.create_time',
        ];

        $builder = $this->modelsManager->createBuilder()
            ->columns($columns)
            ->addFrom(ExamPaperUserModel::class, 'epu')
            ->join(ExamPaperModel::class, 'epu.paper_id = ep.id', 'ep')
            ->join(UserModel::class, 'epu.user_id = user.id', 'user')
            ->where('epu.status = :status:', ['status' => ExamPaperUserModel::STATUS_WAITING])
            ->andWhere('ep.exam_type = :exam_type:', ['exam_type' => ExamPaperModel::EXAM_TYPE_MOCK])
            ->andWhere('ep.teacher_id = :user_id:', ['user_id' => $userId])
            ->andWhere('epu.debut = :debut:', ['debut' => 1])
            ->orderBy('epu.id ASC');

        $pager = new PagerQueryBuilder([
            'builder' => $builder,
            'page' => $page,
            'limit' => $limit,
        ]);

        return $pager->paginate();
    }

}
