<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Caches;

use App\Repos\ExamPaper as ExamPaperRepo;

class ExamPaper extends Cache
{

    protected $lifetime = 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "exam_paper:{$id}";
    }

    public function getContent($id = null)
    {
        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($id);

        return $paper ?: null;
    }

}
