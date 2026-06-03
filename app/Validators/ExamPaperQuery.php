<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\ExamPaper as ExamPaperModel;

class ExamPaperQuery extends Validator
{

    public function checkCategory($id)
    {
        $validator = new Category();

        return $validator->checkCategoryCache($id);
    }

    public function checkTag($id)
    {
        $validator = new Tag();

        return $validator->checkTagCache($id);
    }

    public function checkExamType($type)
    {
        $types = ExamPaperModel::examTypes();

        if (!isset($types[$type])) {
            throw new BadRequestException('exam_paper_query.invalid_exam_type');
        }

        return $type;
    }

    public function checkPackType($type)
    {
        $types = ExamPaperModel::packTypes();

        if (!isset($types[$type])) {
            throw new BadRequestException('exam_paper_query.invalid_pack_type');
        }

        return $type;
    }

    public function checkLevel($level)
    {
        $types = ExamPaperModel::levelTypes();

        if (!isset($types[$level])) {
            throw new BadRequestException('exam_paper_query.invalid_level');
        }

        return $level;
    }

    public function checkSort($sort)
    {
        $types = ExamPaperModel::sortTypes();

        if (!isset($types[$sort])) {
            throw new BadRequestException('exam_paper_query.invalid_sort');
        }

        return $sort;
    }

}
