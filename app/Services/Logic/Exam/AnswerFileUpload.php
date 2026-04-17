<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Services\Logic\Service as LogicService;
use App\Services\MyStorage as StorageService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;
use App\Validators\ExamQuestion as ExamQuestionValidator;

class AnswerFileUpload extends LogicService
{

    public function uploadFile()
    {
        $paperUserId = $this->request->getPost('paper_user_id', ['trim', 'int']);
        $questionId = $this->request->getPost('question_id', ['trim', 'int']);
        $authCode = $this->request->getPost('auth_code', ['trim', 'string']);

        $paperUserValidator = new ExamPaperUserValidator();

        $paperUser = $paperUserValidator->checkById($paperUserId);

        $paperUserValidator->checkAuthCode($paperUser->id, $authCode);

        $questionValidator = new ExamQuestionValidator();

        $question = $questionValidator->checkExamQuestion($questionId);

        $storageService = new StorageService();

        $file = $storageService->uploadExamAnswerFile();

        $cache = $this->getCache();

        $keyName = $this->getCacheKeyName($paperUser->id, $question->id);

        $content = $cache->get($keyName);

        $files = $content ?: [];

        $files[] = [
            'id' => $file->id,
            'name' => $file->name,
            'url' => $storageService->getFileUrl($file->path),
        ];

        $cache->save($keyName, kg_array_unique_multi($files, 'id'), 3600);

        return $file;
    }

    public function fetchFile()
    {
        $paperUserId = $this->request->getQuery('paper_user_id', ['trim', 'int']);
        $questionId = $this->request->getQuery('question_id', ['trim', 'int']);

        $keyName = $this->getCacheKeyName($paperUserId, $questionId);

        $cache = $this->getCache();

        $images = $cache->get($keyName);

        if (!empty($images)) {

            $cache->delete($keyName);

            return $images;
        }

        return [];
    }

    protected function getCacheKeyName($paperUserId, $questionId)
    {
        return sprintf('exam_answer_file_upload:%s_%s', $paperUserId, $questionId);
    }

}
