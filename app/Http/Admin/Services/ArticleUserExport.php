<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ArticleUserList as ArticleUserListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Article as ArticleModel;
use App\Models\ArticleUser as ArticleUserModel;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Validators\Article as ArticleValidator;
use Vtiful\Kernel\Excel;

class ArticleUserExport extends Service
{

    use AccountSearchTrait;

    public function handle($id)
    {
        $article = $this->findArticleOrFail($id);

        $pager = $this->searchArticleUsers($article);

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

        $filename = sprintf('专栏-%s-学员列表-%s.xlsx', $article->title, date('Ymd'));

        $filePath = $excel->fileName($filename)->header($header)->data($rows)->output();

        kg_download($filePath);
    }

    protected function searchArticleUsers(ArticleModel $article)
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['article_id'] = $article->id;
        $params['deleted'] = 0;

        $repo = new ArticleUserRepo();

        $pager = $repo->paginate($params, 'latest', 1, 10000);

        if ($pager->total_items > 0) {
            $builder = new ArticleUserListBuilder();
            $items = $pager->items->toArray();
            $pager->items = $builder->handleUsers($items);
        }

        return $pager;
    }

    protected function getSourceTypeText($type)
    {
        $list = ArticleUserModel::sourceTypes();

        return $list[$type] ?? 'N/A';
    }

    protected function getExpiryTimeText($time)
    {
        return $time > 0 ? date('Y-m-d H:i:s', $time) : 'N/A';
    }

    protected function findArticleOrFail($id)
    {
        $validator = new ArticleValidator();

        return $validator->checkArticle($id);
    }

}
