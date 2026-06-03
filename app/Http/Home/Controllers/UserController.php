<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Url\FullH5Url as FullH5UrlService;
use App\Services\Logic\User\AnswerList as UserAnswerListService;
use App\Services\Logic\User\QuestionList as UserQuestionListService;
use App\Services\Logic\User\StudyArticleList as UserStudyArticleListService;
use App\Services\Logic\User\StudyCourseList as UserStudyCourseListService;
use App\Services\Logic\User\StudyExamPaperList as UserStudyExamPaperListService;
use App\Services\Logic\User\UserInfo as UserInfoService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/user")
 */
class UserController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}", name="home.user.show")
     */
    public function showAction($id)
    {
        $service = new FullH5UrlService();

        if ($service->isMobileBrowser() && $service->h5Enabled()) {
            $location = $service->getUserIndexUrl($id);
            return $this->response->redirect($location);
        }

        $service = new UserInfoService();

        $user = $service->handle($id);

        if ($user['deleted'] == 1) {
            $this->notFound();
        }

        $this->seo->prependTitle(['空间', $user['name']]);

        $this->view->setVar('user', $user);
    }

    /**
     * @Get("/{id:[0-9]+}/study/courses", name="home.user.study_courses")
     */
    public function studyCoursesAction($id)
    {
        $service = new UserStudyCourseListService();

        $pager = $service->handle($id);

        $pager->target = 'tab-study-courses';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('user/study_courses');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/study/articles", name="home.user.study_articles")
     */
    public function studyArticlesAction($id)
    {
        $service = new UserStudyArticleListService();

        $pager = $service->handle($id);

        $pager->target = 'tab-study-articles';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('user/study_articles');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/study/exam/papers", name="home.user.study_exam_papers")
     */
    public function studyExamPapersAction($id)
    {
        $service = new UserStudyExamPaperListService();

        $pager = $service->handle($id);

        $pager->target = 'tab-study-papers';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('user/study_exam_papers');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/questions", name="home.user.questions")
     */
    public function questionsAction($id)
    {
        $service = new UserQuestionListService();

        $pager = $service->handle($id);

        $pager->target = 'tab-questions';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('user/questions');
        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/answers", name="home.user.answers")
     */
    public function answersAction($id)
    {
        $service = new UserAnswerListService();

        $pager = $service->handle($id);

        $pager->target = 'tab-answers';

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('user/answers');
        $this->view->setVar('pager', $pager);
    }

}
