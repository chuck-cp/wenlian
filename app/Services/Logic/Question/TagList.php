<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Question;

use App\Models\Tag as TagModel;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\Tag\ScopedTagList as ScopedTagListService;

class TagList extends LogicService
{

    public function handle()
    {
        $service = new ScopedTagListService();

        return $service->handle(TagModel::SCOPE_QUESTION);
    }

}
