<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Exam\AnswerFileUpload as ExamAnswerFileUploadService;
use App\Services\Logic\Exam\Pilot as ExamPilotService;
use App\Services\Logic\Url\FullH5Url as FullH5UrlService;

/**
 * @RoutePrefix("/exam/answer/file")
 */
class ExamAnswerFileController extends Controller
{

    /**
     * @Get("/fetch", name="home.exam_answer_file.fetch")
     */
    public function fetchAction()
    {
        $service = new ExamAnswerFileUploadService();

        $files = $service->fetchFile();

        return $this->jsonSuccess(['files' => $files]);
    }

    /**
     * @Get("/qrcode", name="home.exam_answer_file.qrcode")
     */
    public function qrcodeAction()
    {
        $paperUserId = $this->request->getQuery('paper_user_id', 'int', 0);
        $questionId = $this->request->getQuery('question_id', 'int', 0);

        $service = new ExamPilotService();

        $authCode = $service->getAuthCode($paperUserId);

        $service = new FullH5UrlService();

        $text = $service->getExamImageUploadUrl([
            'paper_user_id' => $paperUserId,
            'question_id' => $questionId,
            'auth_code' => $authCode,
        ]);

        $qrCode = $this->url->get(
            ['for' => 'home.qrcode'],
            ['text' => urlencode($text)]
        );

        return $this->jsonSuccess(['qrcode' => $qrCode]);
    }

}
