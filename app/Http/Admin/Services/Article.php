<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ArticleList as ArticleListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Http\Admin\Services\Traits\AuthorTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Library\Utils\Word as WordUtil;
use App\Models\Article as ArticleModel;
use App\Models\ArticleTag as ArticleTagModel;
use App\Models\Category as CategoryModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\ArticleTag as ArticleTagRepo;
use App\Repos\Tag as TagRepo;
use App\Services\Category as CategoryService;
use App\Services\Logic\Article\XmTagList as XmTagListService;
use App\Services\Sync\ArticleIndex as ArticleIndexSync;
use App\Traits\Client as ClientTrait;
use App\Validators\Article as ArticleValidator;

class Article extends Service
{

    use ClientTrait;
    use AuthorTrait;
    use AccountSearchTrait;

    public function getXmTags($id)
    {
        $service = new XmTagListService();

        return $service->handle($id);
    }

    public function getCategoryOptions()
    {
        $categoryService = new CategoryService();

        return $categoryService->getCategoryOptions(CategoryModel::TYPE_ARTICLE);
    }

    public function getOwnerOptions()
    {
        return $this->getAuthorOptions();
    }

    public function getStudyExpiryOptions()
    {
        return ArticleModel::studyExpiryOptions();
    }

    public function getSourceTypes()
    {
        return ArticleModel::sourceTypes();
    }

    public function getArticles()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $articleRepo = new ArticleRepo();

        $pager = $articleRepo->paginate($params, $sort, $page, $limit);

        return $this->handleArticles($pager);
    }

    public function getArticle($id)
    {
        return $this->findOrFail($id);
    }

    public function createArticle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $validator = new ArticleValidator();

        $article = new ArticleModel();

        $article->title = $validator->checkTitle($post['title']);
        $article->format = $validator->checkFormat($post['format']);
        $article->client_type = $this->getClientType();
        $article->client_ip = $this->getClientIp();
        $article->owner_id = $user->id;

        $article->create();

        $this->saveDynamicAttrs($article);

        $this->eventsManager->fire('Article:afterCreate', $this, $article);

        return $article;
    }

    public function updateArticle($id)
    {
        $article = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new ArticleValidator();

        $data = [];

        if (isset($post['title'])) {
            $data['title'] = $validator->checkTitle($post['title']);
        }

        if (isset($post['cover'])) {
            $data['cover'] = $validator->checkCover($post['cover']);
        }

        if (isset($post['summary'])) {
            $data['summary'] = $validator->checkSummary($post['summary']);
        }

        if (isset($post['keywords'])) {
            $data['keywords'] = $validator->checkKeywords($post['keywords']);
        }

        if (isset($post['source_type'])) {
            $data['source_type'] = $validator->checkSourceType($post['source_type']);
            if ($post['source_type'] != ArticleModel::SOURCE_ORIGIN) {
                $data['source_url'] = $validator->checkSourceUrl($post['source_url']);
            }
        }

        if (isset($post['fake_user_count'])) {
            $data['fake_user_count'] = $validator->checkUserCount($post['fake_user_count']);
        }

        if (isset($post['market_price'])) {
            $data['market_price'] = $validator->checkMarketPrice($post['market_price']);
        }

        if (isset($post['vip_price'])) {
            $data['vip_price'] = $validator->checkVipPrice($post['vip_price']);
        }

        if (isset($post['study_expiry'])) {
            $data['study_expiry'] = $validator->checkStudyExpiry($post['study_expiry']);
        }

        if (isset($post['featured'])) {
            $data['featured'] = $validator->checkFeatureStatus($post['featured']);
        }

        if (isset($post['closed'])) {
            $data['closed'] = $validator->checkCloseStatus($post['closed']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        if (isset($post['category_id'])) {
            $data['category_id'] = $validator->checkCategoryId($post['category_id']);
        }

        if (isset($post['owner_id'])) {
            $data['owner_id'] = $validator->checkOwnerId($post['owner_id']);
        }

        if (isset($post['xm_tag_ids'])) {
            $this->saveTags($article, $post['xm_tag_ids']);
        }

        if (isset($post['content'])) {
            if ($article->format == 'html') {
                $data['content'] = $validator->checkHtmlContent($post['content']);
            } elseif ($article->format == 'markdown') {
                $data['markdown'] = $validator->checkMarkdownContent($post['content']);
                $data['content'] = kg_parse_markdown($data['markdown']);
            }
        }

        $article->assign($data);

        $article->update();

        $this->saveDynamicAttrs($article);
        $this->syncSaleInfo($article);
        $this->rebuildArticleIndex($article);

        $this->eventsManager->fire('Article:afterUpdate', $this, $article);

        return $article;
    }

    public function deleteArticle($id)
    {
        $article = $this->findOrFail($id);

        $article->deleted = 1;

        $article->update();

        $this->saveDynamicAttrs($article);
        $this->rebuildArticleIndex($article);

        $this->eventsManager->fire('Article:afterDelete', $this, $article);

        return $article;
    }

    public function restoreArticle($id)
    {
        $article = $this->findOrFail($id);

        $article->deleted = 0;

        $article->update();

        $this->saveDynamicAttrs($article);
        $this->rebuildArticleIndex($article);

        $this->eventsManager->fire('Article:afterRestore', $this, $article);

        return $article;
    }

    protected function findOrFail($id)
    {
        $validator = new ArticleValidator();

        return $validator->checkArticle($id);
    }

    protected function rebuildArticleIndex(ArticleModel $article)
    {
        $sync = new ArticleIndexSync();

        $sync->addItem($article->id);
    }

    protected function syncSaleInfo(ArticleModel $article)
    {
        if ($article->hasUpdated(['title', 'cover', 'market_price'])) {
            $sync = new FlashSaleSync();
            $sync->syncArticleInfo($article);

            $sync = new PointGiftSync();
            $sync->syncArticleInfo($article);

            $sync = new GrouponSync();
            $sync->syncArticleInfo($article);

            $sync = new CouponSync();
            $sync->syncArticleInfo($article);

            $sync = new DistributionSync();
            $sync->syncArticleInfo($article);
        }
    }

    protected function saveDynamicAttrs(ArticleModel $article)
    {
        $article->summary = kg_parse_summary($article->content);

        $article->word_count = WordUtil::getWordCount($article->content);

        $article->update();

        /**
         * 重新执行afterFetch
         */
        $article->afterFetch();
    }

    protected function saveTags(ArticleModel $article, $xmTagIds)
    {
        /**
         * 修改数据后，afterFetch设置的属性会失效，重新执行
         */
        $article->afterFetch();

        $originTagIds = [];

        if ($article->tags) {
            $originTagIds = kg_array_column($article->tags, 'id');
        }

        $newTagIds = $xmTagIds ? explode(',', $xmTagIds) : [];
        $addedTagIds = array_diff($newTagIds, $originTagIds);

        if ($addedTagIds) {
            foreach ($addedTagIds as $tagId) {
                $articleTag = new ArticleTagModel();
                $articleTag->article_id = $article->id;
                $articleTag->tag_id = $tagId;
                $articleTag->create();
                $this->recountTagArticles($tagId);
            }
        }

        $deletedTagIds = array_diff($originTagIds, $newTagIds);

        if ($deletedTagIds) {
            $articleTagRepo = new ArticleTagRepo();
            foreach ($deletedTagIds as $tagId) {
                $articleTag = $articleTagRepo->findArticleTag($article->id, $tagId);
                if ($articleTag) {
                    $articleTag->delete();
                    $this->recountTagArticles($tagId);
                }
            }
        }

        $articleTags = [];

        if ($newTagIds) {
            $tagRepo = new TagRepo();
            $tags = $tagRepo->findByIds($newTagIds);
            if ($tags->count() > 0) {
                foreach ($tags as $tag) {
                    $articleTags[] = ['id' => $tag->id, 'name' => $tag->name];
                    $this->recountTagArticles($tag->id);
                }
            }
        }

        $article->tags = $articleTags;

        $article->update();
    }

    protected function recountTagArticles($tagId)
    {
        $tagRepo = new TagRepo();

        $tag = $tagRepo->findById($tagId);

        if (!$tag) return;

        $articleCount = $tagRepo->countArticles($tagId);

        $tag->article_count = $articleCount;

        $tag->update();
    }

    protected function handleArticles($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ArticleListBuilder();

            $items = $pager->items->toArray();

            $pipeA = $builder->handleArticles($items);
            $pipeB = $builder->handleCategories($pipeA);
            $pipeC = $builder->handleUsers($pipeB);
            $pipeD = $builder->objects($pipeC);

            $pager->items = $pipeD;
        }

        return $pager;
    }

}
