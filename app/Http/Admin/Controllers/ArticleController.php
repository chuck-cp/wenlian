<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Article as ArticleService;
use App\Http\Admin\Services\ArticleUser as ArticleUserService;
use App\Http\Admin\Services\ArticleUserExport as ArticleUserExportService;
use App\Http\Admin\Services\ArticleUserImport as ArticleUserImportService;
use App\Http\Admin\Services\User as UserService;
use App\Models\Category as CategoryModel;

/**
 * @RoutePrefix("/admin/article")
 */
class ArticleController extends Controller
{

    /**
     * @Get("/category", name="admin.article.category")
     */
    public function categoryAction()
    {
        $location = $this->url->get(
            ['for' => 'admin.category.list'],
            ['type' => CategoryModel::TYPE_ARTICLE]
        );

        return $this->response->redirect($location);
    }

    /**
     * @Get("/search", name="admin.article.search")
     */
    public function searchAction()
    {
        $articleService = new ArticleService();

        $categoryOptions = $articleService->getCategoryOptions();
        $sourceTypes = $articleService->getSourceTypes();

        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('source_types', $sourceTypes);
    }

    /**
     * @Get("/list", name="admin.article.list")
     */
    public function listAction()
    {
        $articleService = new ArticleService();

        $pager = $articleService->getArticles();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/add", name="admin.article.add")
     */
    public function addAction()
    {

    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.article.edit")
     */
    public function editAction($id)
    {
        $articleService = new ArticleService();

        $studyExpiryOptions = $articleService->getStudyExpiryOptions();
        $categoryOptions = $articleService->getCategoryOptions();
        $ownerOptions = $articleService->getOwnerOptions();
        $sourceTypes = $articleService->getSourceTypes();
        $xmTags = $articleService->getXmTags($id);
        $article = $articleService->getArticle($id);

        $this->view->setVar('study_expiry_options', $studyExpiryOptions);
        $this->view->setVar('category_options', $categoryOptions);
        $this->view->setVar('owner_options', $ownerOptions);
        $this->view->setVar('source_types', $sourceTypes);
        $this->view->setVar('xm_tags', $xmTags);
        $this->view->setVar('article', $article);
    }

    /**
     * @Get("/{id:[0-9]+}/show", name="admin.article.show")
     */
    public function showAction($id)
    {
        $articleService = new ArticleService();

        $article = $articleService->getArticle($id);

        $this->view->setVar('article', $article);
    }

    /**
     * @Post("/create", name="admin.article.create")
     */
    public function createAction()
    {
        $articleService = new ArticleService();

        $article = $articleService->createArticle();

        $location = $this->url->get([
            'for' => 'admin.article.edit',
            'id' => $article->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '创建文章成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.article.update")
     */
    public function updateAction($id)
    {
        $articleService = new ArticleService();

        $articleService->updateArticle($id);

        $content = ['msg' => '更新文章成功'];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.article.delete")
     */
    public function deleteAction($id)
    {
        $articleService = new ArticleService();

        $articleService->deleteArticle($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '删除文章成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.article.restore")
     */
    public function restoreAction($id)
    {
        $articleService = new ArticleService();

        $articleService->restoreArticle($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '还原文章成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/users", name="admin.article.users")
     */
    public function usersAction($id)
    {
        $service = new ArticleService();
        $article = $service->getArticle($id);

        $service = new ArticleUserService();
        $pager = $service->getUsers($id);

        $this->view->setVar('article', $article);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/user/search", name="admin.article.search_user")
     */
    public function searchUserAction($id)
    {
        $service = new ArticleService();
        $article = $service->getArticle($id);

        $service = new ArticleUserService();
        $sourceTypes = $service->getSourceTypes();

        $this->view->pick('article/search_user');
        $this->view->setVar('source_types', $sourceTypes);
        $this->view->setVar('article', $article);
    }

    /**
     * @Get("/{id:[0-9]+}/user/add", name="admin.article.add_user")
     */
    public function addUserAction($id)
    {
        $service = new ArticleService();

        $article = $service->getArticle($id);

        $this->view->pick('article/add_user');
        $this->view->setVar('article', $article);
    }

    /**
     * @Post("/{id:[0-9]+}/user/create", name="admin.article.create_user")
     */
    public function createUserAction($id)
    {
        $service = new ArticleUserService();

        $service->create();

        $location = $this->url->get([
            'for' => 'admin.article.users',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/user/import", name="admin.article.import_user")
     */
    public function importUserAction($id)
    {
        $importService = new ArticleUserImportService();

        $importService->handle($id);

        $location = $this->url->get([
            'for' => 'admin.article.users',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '导入学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Get("/{id:[0-9]+}/user/export", name="admin.article.export_user")
     */
    public function exportUserAction($id)
    {
        $exportService = new ArticleUserExportService();

        $result = $exportService->handle($id);

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.article.search_user', 'id' => $id],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/user/edit/{id:[0-9]+}", name="admin.article.edit_user")
     */
    public function editUserAction($id)
    {
        $service = new ArticleUserService();
        $articleUser = $service->get($id);

        $service = new ArticleService();
        $article = $service->getArticle($articleUser->article_id);

        $service = new UserService();
        $user = $service->getUser($articleUser->user_id);

        $this->view->pick('article/edit_user');
        $this->view->setVar('article_user', $articleUser);
        $this->view->setVar('article', $article);
        $this->view->setVar('user', $user);
    }

    /**
     * @Post("/user/update/{id:[0-9]+}", name="admin.article.update_user")
     */
    public function updateUserAction($id)
    {
        $service = new ArticleUserService();

        $service->update($id);

        $content = ['msg' => '更新学员成功'];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/user/delete/{id:[0-9]+}", name="admin.article.delete_user")
     */
    public function deleteUserAction($id)
    {
        $service = new ArticleUserService();

        $service->delete($id);

        $content = [
            'location' => $this->request->getHTTPReferer(),
            'msg' => '删除学员成功',
        ];

        return $this->jsonSuccess($content);
    }

}
