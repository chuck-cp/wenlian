<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\User as UserModel;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;

class UnitPaperJoin extends LogicService
{

    use ExamPaperTrait;
    use PaperUserTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaper($id);

        $user = $this->getLoginUser();

        $this->setPaperUser($paper, $user);

        $this->handlePaperUser($paper, $user);

        return $this->debutPaperUser;
    }

    protected function handlePaperUser(ExamPaperModel $paper, UserModel $user)
    {
        if ($user->id == 0) return;

        if (!$this->ownedPaper) return;

        $debutPaperUser = $this->debutPaperUser;

        try {

            $this->db->begin();

            if (!$debutPaperUser) {
                $debutPaperUser = $this->assignPaperDebut($paper, $user);
                $this->debutPaperUser = $debutPaperUser;
            }

            if ($debutPaperUser->start_time == 0) {
                $this->startPaperUser($debutPaperUser);
            }

            if (!$this->joinedPaper) {
                $this->recountPaperJoins($paper);
                $this->recountUserStudyPapers($user);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Join Unit Exam Paper Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

    protected function assignPaperDebut(ExamPaperModel $paper, UserModel $user)
    {
        $paperUser = new ExamPaperUserModel();

        $paperUser->user_id = $user->id;
        $paperUser->paper_id = $paper->id;
        $paperUser->paper_duration = $paper->duration * 60;
        $paperUser->paper_score = $paper->total_score;
        $paperUser->pass_score = $paper->pass_score;
        $paperUser->source_type = $this->getFreeSourceType($paper, $user);
        $paperUser->debut = 1;

        $paperUser->create();

        return $paperUser;
    }

    protected function startPaperUser(ExamPaperUserModel $paperUser)
    {
        $paperUser->start_time = time();

        $paperUser->status = ExamPaperUserModel::STATUS_ACTIVE;

        $paperUser->update();
    }

}
