<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\Chapter as ChapterModel;
use App\Models\Course as CourseModel;
use App\Validators\Course as CourseValidator;

class CourseImport extends Service
{

    public function handle()
    {
        $path = $this->request->getPost('path');

        $validator = new CourseValidator();

        $validator->checkImportTemplate($path);

        $content = file_get_contents($path);

        ['title' => $title, 'model' => $model] = $this->parseTitleAndModel($content);

        $model = $this->getModelType($model);

        $title = $validator->checkTitle($title);

        $model = $validator->checkModel($model);

        $validator->checkIfDuplicate($title);

        $chapters = $this->parseChapters($content);

        try {

            $this->db->begin();

            $course = new CourseModel();

            $course->title = $title;
            $course->model = $model;

            $course->create();

            $courseLessonCount = 0;

            foreach ($chapters as $key => $value) {

                $chapter = new ChapterModel();

                $chapter->title = $value['title'];
                $chapter->course_id = $course->id;
                $chapter->parent_id = 0;
                $chapter->priority = $key; // 键值从1开始
                $chapter->model = $model;

                $chapter->create();

                $chapterLessonCount = 0;

                foreach ($value['lessons'] as $subKey => $subValue) {

                    $courseLessonCount++;
                    $chapterLessonCount++;

                    $chapterModel = $this->getModelType($subValue['model']);

                    $lesson = new ChapterModel();

                    $lesson->title = $subValue['title'];
                    $lesson->course_id = $course->id;
                    $lesson->parent_id = $chapter->id;
                    $lesson->priority = $subKey + 1; //键值从0开始
                    $lesson->model = $chapterModel ?: $model;

                    $lesson->create();
                }

                $chapter->lesson_count = $chapterLessonCount;

                $chapter->update();
            }

            $course->lesson_count = $courseLessonCount;

            $course->update();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('http');

            $logger->error('Import Course Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

    protected function parseTitleAndModel($content)
    {
        $titleFlag = '## '; // 标题标识

        $quoteTitleFlag = preg_quote($titleFlag);

        preg_match("/{$quoteTitleFlag}(.*?)<(.*?)>/", $content, $matches);

        $result = ['title' => '', 'model' => ''];

        if (isset($matches[1]) && $matches[2]) {
            $result = [
                'title' => trim($matches[1]),
                'model' => trim($matches[2]),
            ];
        }

        return $result;
    }

    protected function parseChapters($content)
    {
        $lines = explode(PHP_EOL, $content);

        foreach ($lines as $key => $value) {
            $value = trim($value);
            if (empty($value)) unset($lines[$key]);
        }

        $chapters = [];

        $chapterFlag = '### '; // 章标识
        $lessonFlag = '* '; // 节标识

        $i = 0;

        foreach ($lines as $line) {
            if (strpos($line, $chapterFlag) !== false) {
                $i++; // 必须在首位
                $chapters[$i]['title'] = str_replace($chapterFlag, '', $line);
            } elseif (strpos($line, $lessonFlag) !== false) {
                $quoteLessonFlag = preg_quote($lessonFlag);
                preg_match("/{$quoteLessonFlag}(.*?)<(.*?)>/", $line, $matches);
                if (isset($matches[1]) && isset($matches[2])) {
                    $chapters[$i]['lessons'][] = [
                        'title' => trim($matches[1]),
                        'model' => trim($matches[2]),
                    ];
                } else {
                    $chapters[$i]['lessons'][] = [
                        'title' => str_replace($lessonFlag, '', $line),
                        'model' => '',
                    ];
                }
            }
        }

        return $chapters;
    }

    protected function getModelType($text)
    {
        $result = CourseModel::MODEL_VOD;

        $types = CourseModel::modelTypes();

        foreach ($types as $key => $value) {
            if ($text == $value) {
                $result = $key;
            }
        }

        return $result;
    }

}
