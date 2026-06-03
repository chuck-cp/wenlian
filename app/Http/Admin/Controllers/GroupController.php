<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Group as GroupService;
use App\Http\Admin\Services\GroupArticle as GroupArticleService;
use App\Http\Admin\Services\GroupCourse as GroupCourseService;
use App\Http\Admin\Services\GroupExamPaper as GroupExamPaperService;
use App\Http\Admin\Services\GroupUser as GroupUserService;
use App\Http\Admin\Services\GroupUserImport as GroupUserImportService;

/**
 * @RoutePrefix("/admin/group")
 */
class GroupController extends Controller
{

    /**
     * @Get("/list", name="admin.group.list")
     */
    public function listAction()
    {
        $groupService = new GroupService();

        $pager = $groupService->getGroups();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/add", name="admin.group.add")
     */
    public function addAction()
    {

    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.group.edit")
     */
    public function editAction($id)
    {
        $groupService = new GroupService;

        $group = $groupService->getGroup($id);

        $this->view->setVar('group', $group);
    }

    /**
     * @Get("/{id:[0-9]+}/users", name="admin.group.users")
     */
    public function usersAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupUserService = new GroupUserService();
        $pager = $groupUserService->getUsers($id);

        $this->view->setVar('group', $group);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/courses", name="admin.group.courses")
     */
    public function coursesAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupCourseService = new GroupCourseService();
        $pager = $groupCourseService->getCourses($id);

        $this->view->setVar('group', $group);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/exam/papers", name="admin.group.exam_papers")
     */
    public function examPapersAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupPaperService = new GroupExamPaperService();
        $pager = $groupPaperService->getExamPapers($id);

        $this->view->pick('group/exam_papers');
        $this->view->setVar('group', $group);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/articles", name="admin.group.articles")
     */
    public function articlesAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupArticleService = new GroupArticleService();
        $pager = $groupArticleService->getArticles($id);

        $this->view->setVar('group', $group);
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/user/add", name="admin.group.add_user")
     */
    public function addUserAction($id)
    {
        $groupService = new GroupService();

        $group = $groupService->getGroup($id);

        $this->view->pick('group/add_user');
        $this->view->setVar('group', $group);
    }

    /**
     * @Get("/{id:[0-9]+}/course/add", name="admin.group.add_course")
     */
    public function addCourseAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupCourseService = new GroupCourseService();
        $xmCourses = $groupCourseService->getXmCourses();

        $this->view->pick('group/add_course');
        $this->view->setVar('group', $group);
        $this->view->setVar('xm_courses', $xmCourses);
    }

    /**
     * @Get("/{id:[0-9]+}/exam/paper/add", name="admin.group.add_exam_paper")
     */
    public function addExamPaperAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupPaperService = new GroupExamPaperService();
        $xmExamPapers = $groupPaperService->getXmExamPapers();

        $this->view->pick('group/add_exam_paper');
        $this->view->setVar('group', $group);
        $this->view->setVar('xm_exam_papers', $xmExamPapers);
    }

    /**
     * @Get("/{id:[0-9]+}/article/add", name="admin.group.add_article")
     */
    public function addArticleAction($id)
    {
        $groupService = new GroupService();
        $group = $groupService->getGroup($id);

        $groupArticleService = new GroupArticleService();
        $xmArticles = $groupArticleService->getXmArticles();

        $this->view->pick('group/add_article');
        $this->view->setVar('group', $group);
        $this->view->setVar('xm_articles', $xmArticles);
    }

    /**
     * @Post("/create", name="admin.group.create")
     */
    public function createAction()
    {
        $groupService = new GroupService();

        $group = $groupService->createGroup();

        $location = $this->url->get([
            'for' => 'admin.group.edit',
            'id' => $group->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '创建分组成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.group.update")
     */
    public function updateAction($id)
    {
        $groupService = new GroupService();

        $groupService->updateGroup($id);

        $location = $this->url->get(['for' => 'admin.group.list']);

        $content = [
            'location' => $location,
            'msg' => '更新分组成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.group.delete")
     */
    public function deleteAction($id)
    {
        $groupService = new GroupService();

        $groupService->deleteGroup($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除分组成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.group.restore")
     */
    public function restoreAction($id)
    {
        $groupService = new GroupService();

        $groupService->restoreGroup($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '还原分组成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/user/create", name="admin.group.create_user")
     */
    public function createUserAction($id)
    {
        $groupUserService = new GroupUserService();

        $groupUserService->create();

        $location = $this->url->get([
            'for' => 'admin.group.users',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/user/import", name="admin.group.import_user")
     */
    public function importUserAction($id)
    {
        $importService = new GroupUserImportService();

        $importService->handle($id);

        $location = $this->url->get([
            'for' => 'admin.group.users',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '导入学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/user/delete/{id:[0-9]+}", name="admin.group.delete_user")
     */
    public function deleteUserAction($id)
    {
        $groupUserService = new GroupUserService();

        $groupUserService->delete($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除学员成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/course/create", name="admin.group.create_course")
     */
    public function createCourseAction($id)
    {
        $groupCourseService = new GroupCourseService();

        $groupCourseService->create();

        $location = $this->url->get([
            'for' => 'admin.group.courses',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加课程成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/course/delete/{id:[0-9]+}", name="admin.group.delete_course")
     */
    public function deleteCourseAction($id)
    {
        $groupCourseService = new GroupCourseService();

        $groupCourseService->delete($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除课程成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/exam/paper/create", name="admin.group.create_exam_paper")
     */
    public function createExamPaperAction($id)
    {
        $groupPaperService = new GroupExamPaperService();

        $groupPaperService->create();

        $location = $this->url->get([
            'for' => 'admin.group.exam_papers',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加试卷成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/exam/paper/delete/{id:[0-9]+}", name="admin.group.delete_exam_paper")
     */
    public function deleteExamPaperAction($id)
    {
        $groupPaperService = new GroupExamPaperService();

        $groupPaperService->delete($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除试卷成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/article/create", name="admin.group.create_article")
     */
    public function createArticleAction($id)
    {
        $groupArticleService = new GroupArticleService();

        $groupArticleService->create();

        $location = $this->url->get([
            'for' => 'admin.group.articles',
            'id' => $id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '添加专栏成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/article/delete/{id:[0-9]+}", name="admin.group.delete_article")
     */
    public function deleteArticleAction($id)
    {
        $groupArticleService = new GroupArticleService();

        $groupArticleService->delete($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除专栏成功',
        ];

        return $this->jsonSuccess($content);
    }

}
