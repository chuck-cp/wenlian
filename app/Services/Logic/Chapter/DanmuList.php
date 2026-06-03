<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Chapter;

use App\Models\Danmu as DanmuModel;
use App\Repos\Danmu as DanmuRepo;
use App\Services\Logic\ChapterTrait;
use App\Services\Logic\Service as LogicService;

class DanmuList extends LogicService
{

    use ChapterTrait;

    public function handle($id)
    {
        $chapter = $this->checkChapter($id);

        $params = [];

        $params['chapter_id'] = $chapter->id;
        $params['published'] = DanmuModel::PUBLISH_APPROVED;
        $params['deleted'] = 0;

        $danmuRepo = new DanmuRepo();

        $items = $danmuRepo->findAll($params);

        $result = [];

        if ($items->count() > 0) {
            $result = $this->handleItems($items->toArray());
        }

        return $result;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function handleItems($items)
    {
        $result = [];

        foreach ($items as $item) {

            $result[] = [
                'id' => $item['id'],
                'text' => $item['text'],
                'color' => $item['color'],
                'size' => $item['size'],
                'time' => $item['time'],
                'type' => $item['type'],
            ];
        }

        return $result;
    }

}
