<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Caches\CategoryList as CategoryListCache;
use App\Models\Category as CategoryModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use Vtiful\Kernel\Excel;

class ExamQuestionImport extends Service
{

    protected $categoryList = [];

    public function handle()
    {
        set_time_limit(300);

        ini_set('memory_limit', '512M');

        $this->categoryList = $this->getCategoryList();

        $path = $this->request->getPost('path');

        $dirname = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_BASENAME);

        $excel = new Excel(['path' => $dirname]);

        $rows = $excel
            ->openFile($filename)
            ->openSheet(null, Excel::SKIP_EMPTY_ROW)
            ->getSheetData();

        if (count($rows) < 3) return;

        foreach ($rows as $key => $value) {
            if ($key > 1) {
                $this->handleRow($value);
            }
        }
    }

    protected function handleRow($row)
    {
        $category = $this->handleColumn($row[0]);
        $model = $this->handleColumn($row[1]);
        $level = $this->handleColumn($row[2]);
        $topic = $this->handleColumn($row[3]);
        $answer = $this->handleColumn($row[4]);
        $solution = $this->handleColumn($row[5]);
        $score = $this->handleColumn($row[6]);
        $choiceA = $this->handleColumn($row[7]);
        $choiceB = $this->handleColumn($row[8]);
        $choiceC = $this->handleColumn($row[9]);
        $choiceD = $this->handleColumn($row[10]);

        if (empty($topic)) return;

        $model = $this->getModel($model);

        if (empty($model)) return;

        $level = $this->getLevel($level);
        $categoryId = $this->getCategoryId($category);

        $question = new ExamQuestionModel();

        $question->topic = $topic;
        $question->solution = $solution;
        $question->model = (int)$model;
        $question->level = (int)$level;
        $question->score = (int)$score;
        $question->category_id = (int)$categoryId;

        if ($model == ExamQuestionModel::MODEL_SINGLE_CHOICE) {

            $choices = $this->handleChoices($choiceA, $choiceB, $choiceC, $choiceD);

            if (count($choices) < 2) return;

            $question->attrs = ['choices' => $choices];
            $question->answer = $this->handleSingleChoiceAnswer($answer);

        } elseif ($model == ExamQuestionModel::MODEL_MULTIPLE_CHOICE) {

            $choices = $this->handleChoices($choiceA, $choiceB, $choiceC, $choiceD);

            if (count($choices) < 2) return;

            $question->attrs = ['choices' => $choices];
            $question->answer = $this->handleMultipleChoiceAnswer($answer);

        } elseif ($model == ExamQuestionModel::MODEL_TRUE_FALSE) {

            $question->answer = $this->handleTrueFalseAnswer($answer);

        } elseif ($model == ExamQuestionModel::MODEL_BLANK_FILL) {

            $question->answer = $this->handleBlankFillAnswer($answer);

        } elseif ($model == ExamQuestionModel::MODEL_SHORT_ANSWER) {

            $question->answer = $answer;
        }

        try {

            $question->create();

        } catch (\Exception $e) {

            $logger = $this->getLogger();

            $logger->error('Import Exam Question Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'row' => $row,
                ]));
        }
    }

    protected function handleChoices($c1, $c2, $c3, $c4)
    {
        $choices = [
            'A' => $c1,
            'B' => $c2,
            'C' => $c3,
            'D' => $c4,
        ];

        if (empty($c1)) unset($choices['A']);
        if (empty($c2)) unset($choices['B']);
        if (empty($c3)) unset($choices['C']);
        if (empty($c4)) unset($choices['D']);

        return $choices;
    }

    protected function getModel($name)
    {
        $result = 0;

        foreach (ExamQuestionModel::modelTypes() as $key => $value) {
            if ($value == $name) {
                return $key;
            }
        }

        return $result;
    }

    protected function getLevel($name)
    {
        $result = 0;

        foreach (ExamQuestionModel::levelTypes() as $key => $value) {
            if ($value == $name) {
                return $key;
            }
        }

        return $result;
    }

    protected function getCategoryId($name)
    {
        $result = 0;

        if (!$this->categoryList) return $result;

        foreach ($this->categoryList as $category) {
            if ($category['name'] == $name) {
                return $category['id'];
            }
        }

        return $result;
    }

    protected function handleColumn($value)
    {
        return $this->filter->sanitize($value, ['trim', 'string']);
    }

    protected function handleSingleChoiceAnswer($answer)
    {
        /**
         * 替换中文逗号和空格
         */
        return str_replace(['，', '　'], [',', ''], $answer);
    }

    protected function handleMultipleChoiceAnswer($answer)
    {
        /**
         * 替换中文逗号和空格
         */
        $answer = str_replace(['，', '　'], [',', ''], $answer);

        $answer = trim($answer, ',');

        $answer = strtoupper($answer);

        /**
         * 兼容没有分隔符的情况
         */
        if (strpos($answer, ',') !== false) {
            $list = explode(',', $answer);
        } else {
            $list = str_split($answer);
        }

        sort($list);

        return implode(',', $list);
    }

    protected function handleTrueFalseAnswer($answer)
    {
        /**
         * 替换中文空格
         */
        $answer = str_replace(['　'], [''], $answer);

        return $answer == '正确' ? 'T' : 'F';
    }

    protected function handleBlankFillAnswer($answer)
    {
        /**
         * 替换中文逗号和空格
         */
        $answer = str_replace(['，', '　'], [',', ''], $answer);

        return trim($answer, ',');
    }

    protected function getCategoryList()
    {
        $cache = new CategoryListCache();

        return $cache->get(CategoryModel::TYPE_EXAM_QUESTION);
    }

}
