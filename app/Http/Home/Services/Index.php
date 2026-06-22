<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Services;

use App\Caches\IndexFeaturedCourseList;
use App\Caches\IndexFreeCourseList;
use App\Caches\IndexLiveList;
use App\Caches\IndexNewCourseList;
use App\Caches\IndexSimpleFeaturedCourseList;
use App\Caches\IndexSimpleFreeCourseList;
use App\Caches\IndexSimpleNewCourseList;
use App\Caches\IndexSimpleVipCourseList;
use App\Caches\IndexSlideList;
use App\Caches\IndexVipCourseList;
use App\Models\Course as CourseModel;
use App\Models\Slide as SlideModel;
use App\Repos\Course as CourseRepo;
use App\Repos\Topic as TopicRepo;

class Index extends Service
{

    public function getSlides()
    {
        $cache = new IndexSlideList();

        /**
         * @var array $slides
         */
        $slides = $cache->get();

        if (empty($slides)) return [];

        foreach ($slides as $key => $slide) {
            switch ($slide['target']) {
                case SlideModel::TARGET_EXAM_PAPER:
                    $slides[$key]['url'] = $this->url->get([
                        'for' => 'home.exam_paper.show',
                        'id' => $slide['content'],
                    ]);
                    break;
                case SlideModel::TARGET_ARTICLE:
                    $slides[$key]['url'] = $this->url->get([
                        'for' => 'home.article.show',
                        'id' => $slide['content'],
                    ]);
                    break;
                case SlideModel::TARGET_COURSE:
                    $slides[$key]['url'] = $this->url->get([
                        'for' => 'home.course.show',
                        'id' => $slide['content'],
                    ]);
                    break;
                case SlideModel::TARGET_PAGE:
                    $slides[$key]['url'] = $this->url->get([
                        'for' => 'home.page.show',
                        'id' => $slide['content'],
                    ]);
                    break;
                case SlideModel::TARGET_LINK:
                    $slides[$key]['url'] = $slide['content'];
                    break;
            }
        }

        return $slides;
    }

    public function getLives()
    {
        $cache = new IndexLiveList();

        return $cache->get();
    }

    public function getFeaturedCourses()
    {
        $cache = new IndexFeaturedCourseList();

        $courses = $cache->get();

        return $this->handleCategoryCourses($courses);
    }

    public function getNewCourses()
    {
        $cache = new IndexNewCourseList();

        $courses = $cache->get();

        return $this->handleCategoryCourses($courses);
    }

    public function getFreeCourses()
    {
        $cache = new IndexFreeCourseList();

        $courses = $cache->get();

        return $this->handleCategoryCourses($courses);
    }

    public function getVipCourses()
    {
        $cache = new IndexVipCourseList();

        $courses = $cache->get();

        return $this->handleCategoryCourses($courses);
    }

    public function getSimpleNewCourses()
    {
        $cache = new IndexSimpleNewCourseList();

        return $cache->get();
    }

    public function getSimpleFeaturedCourses()
    {
        $cache = new IndexSimpleFeaturedCourseList();

        return $cache->get();
    }

    public function getSimpleFreeCourses()
    {
        $cache = new IndexSimpleFreeCourseList();

        return $cache->get();
    }

    public function getSimpleVipCourses()
    {
        $cache = new IndexSimpleVipCourseList();

        return $cache->get();
    }

    /**
     * 首页专题块：每块为专题标题 + 最多 8 门已上架关联课程（无已上架关联课程则不展示该块）。
     *
     * @param int $maxTopics
     * @param int $coursesPerTopic
     * @param bool $publishedOnly
     * @param bool $withUrl
     * @return array<int, array{id:int,title:string,url?:string,courses:array}>
     */
    public function getIndexTopicSections($maxTopics = 20, $coursesPerTopic = 8, $publishedOnly = false, $withUrl = true)
    {
        $topicRepo = new TopicRepo();

        $topics = $topicRepo->findTopicsForHomeIndex($maxTopics, $publishedOnly);

        $sections = [];

        $courseRepo = new CourseRepo();

        foreach ($topics as $topic) {
            $orderedIds = $topicRepo->findCourseIdsForTopicHomeIndex($topic->id, $coursesPerTopic);

            if (count($orderedIds) === 0) {
                continue;
            }

            $result = $courseRepo->findByIds($orderedIds);

            $byId = [];

            foreach ($result as $course) {
                if (!$course instanceof CourseModel) {
                    continue;
                }

                $byId[(int)$course->id] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'cover' => $course->cover,
                    'model' => (int)$course->model,
                    'rating' => (float)$course->rating,
                    'market_price' => (float)$course->market_price,
                    'vip_price' => (float)$course->vip_price,
                    'lesson_count' => (int)$course->lesson_count,
                    'user_count' => (int)$course->user_count,
                ];
            }

            $courses = [];

            foreach ($orderedIds as $cid) {
                if (isset($byId[$cid])) {
                    $courses[] = $byId[$cid];
                }
            }

            if (count($courses) === 0) {
                continue;
            }

            $section = [
                'id' => $topic->id,
                'title' => $topic->title,
                'courses' => $courses,
            ];

            if ($withUrl) {
                $section['url'] = $this->url->get([
                    'for' => 'home.topic.show',
                    'id' => $topic->id,
                ]);
            }

            $sections[] = $section;
        }

        return $sections;
    }

    protected function handleCategoryCourses($items, $limit = 8)
    {
        if (count($items) == 0) {
            return [];
        }

        foreach ($items as &$item) {
            $item['courses'] = array_slice($item['courses'], 0, $limit);
        }

        return $items;
    }

}
