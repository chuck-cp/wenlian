<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Http\Home\Services\ArticleQuery as ArticleQueryService;
use App\Services\Logic\Article\ArticleFavorite as ArticleFavoriteService;
use App\Services\Logic\Article\ArticleInfo as ArticleInfoService;
use App\Services\Logic\Article\ArticleLike as ArticleLikeService;
use App\Services\Logic\Article\ArticleList as ArticleListService;
use App\Services\Logic\Article\RelatedArticleList as RelatedArticleListService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/article")
 */
class ArticleController extends Controller
{

    /**
     * @Get("/list", name="home.article.list")
     */
    public function listAction()
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getArticleListUrl();
            return $this->response->redirect($location);
        }

        $service = new ArticleQueryService();

        $topCategories = $service->handleTopCategories();
        $subCategories = $service->handleSubCategories();
        $sourceTypes = $service->handleSourceTypes();
        $sorts = $service->handleSorts();
        $params = $service->getParams();

        $this->seo->prependTitle('专栏');

        $this->view->setVar('top_categories', $topCategories);
        $this->view->setVar('sub_categories', $subCategories);
        $this->view->setVar('source_types', $sourceTypes);
        $this->view->setVar('sorts', $sorts);
        $this->view->setVar('params', $params);
    }

    /**
     * @Get("/pager", name="home.article.pager")
     */
    public function pagerAction()
    {
        $service = new ArticleListService();

        $pager = $service->handle();

        $pager->target = 'article-list';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}", name="home.article.show")
     */
    public function showAction($id)
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getArticleInfoUrl($id);
            return $this->response->redirect($location);
        }

        $service = new ArticleInfoService();

        $article = $service->handle($id);

        if ($article['deleted'] == 1) {
            $this->notFound();
        }

        if ($article['published'] == 0) {
            $this->notFound();
        }

        $this->seo->prependTitle(['专栏', $article['title']]);
        $this->seo->setKeywords($article['keywords']);
        $this->seo->setDescription($article['summary']);

        $this->view->setVar('article', $article);
    }

    /**
     * @Get("/{id:[0-9]+}/related", name="home.article.related")
     */
    public function relatedAction($id)
    {
        $service = new RelatedArticleListService();

        $articles = $service->handle($id);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('articles', $articles);
    }

    /**
     * @Post("/{id:[0-9]+}/favorite", name="home.article.favorite")
     */
    public function favoriteAction($id)
    {
        $service = new ArticleFavoriteService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '收藏成功' : '取消收藏成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

    /**
     * @Post("/{id:[0-9]+}/like", name="home.article.like")
     */
    public function likeAction($id)
    {
        $service = new ArticleLikeService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '点赞成功' : '取消点赞成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

}
