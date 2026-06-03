<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgOwnership as KgOwnershipModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\ExamQuestionUser as ExamQuestionUserModel;
use App\Models\User as UserModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;

class MockPaperJoin extends LogicService
{

    use ExamPaperTrait;
    use PaperUserTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaper($id);

        $user = $this->getLoginUser();

        $this->setPaperUser($paper, $user);

        $this->handlePaperUser($paper, $user);

        return $this->currentPaperUser;
    }

    protected function handlePaperUser(ExamPaperModel $paper, UserModel $user)
    {
        if ($user->id == 0) return;

        if (!$this->ownedPaper) return;

        $debutPaperUser = $this->debutPaperUser;

        try {

            $this->db->begin();

            if ($debutPaperUser) { // 已拥有首考资格

                $this->currentPaperUser = $debutPaperUser;

                if ($debutPaperUser->start_time == 0) { // 首次考试

                    $this->startPaperUser($debutPaperUser);
                    $this->assignQuestions($debutPaperUser, $paper);

                } elseif ($debutPaperUser->end_time > 0) { // 首考已结束，再次考试

                    $againPaperUser = $this->assignPaperAgain($paper, $user);

                    $this->startPaperUser($againPaperUser);
                    $this->assignQuestions($againPaperUser, $paper);

                    $this->currentPaperUser = $againPaperUser;
                }

            } else {

                $debutPaperUser = $this->assignPaperDebut($paper, $user);

                $this->startPaperUser($debutPaperUser);
                $this->assignQuestions($debutPaperUser, $paper);

                $this->debutPaperUser = $debutPaperUser;
                $this->currentPaperUser = $debutPaperUser;
            }

            if (!$this->joinedPaper) {
                $this->recountPaperJoins($paper);
                $this->recountUserStudyPapers($user);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Join Exam Paper Exception: ' . kg_json_encode([
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

    protected function assignPaperAgain(ExamPaperModel $paper, UserModel $user)
    {
        $paperUser = new ExamPaperUserModel();

        $paperUser->paper_id = $paper->id;
        $paperUser->user_id = $user->id;
        $paperUser->paper_duration = $paper->duration * 60;
        $paperUser->paper_score = $paper->total_score;
        $paperUser->pass_score = $paper->pass_score;
        $paperUser->source_type = KgOwnershipModel::SOURCE_FREE;
        $paperUser->debut = 0;

        $paperUser->create();

        return $paperUser;
    }

    protected function assignQuestions(ExamPaperUserModel $paperUser, ExamPaperModel $paper)
    {
        if ($paper->pack_type == ExamPaperModel::PACK_TYPE_MANUAL) {
            $this->assignManualQuestions($paperUser, $paper);
        } elseif ($paper->pack_type == ExamPaperModel::PACK_TYPE_RANDOM) {
            $this->assignRandomQuestions($paperUser, $paper);
        }
    }

    protected function assignManualQuestions(ExamPaperUserModel $paperUser, ExamPaperModel $paper)
    {
        $paperRepo = new ExamPaperRepo();

        $questions = $paperRepo->findQuestions($paper->id);

        if ($questions->count() == 0) {
            throw new \RuntimeException("No Questions on Paper: {$paper->id}");
        }

        $this->createQuestionUsers($paperUser, $questions);
    }

    protected function assignRandomQuestions(ExamPaperUserModel $paperUser, ExamPaperModel $paper)
    {
        $questionRepo = new ExamQuestionRepo();

        $allQuestions = $questionRepo->findByCategoryIds($paper->attrs['category_ids']);

        if ($allQuestions->count() == 0) {
            throw new \RuntimeException("No Questions on Paper: {$paper->id}");
        }

        $questionIds = [];

        foreach ($paper->attrs['conditions'] as $condition) {
            if ($condition['limit'] > 0) {
                $randQuestionIds = $this->getRandQuestionIds($allQuestions, $condition);
                $questionIds = array_merge($questionIds, $randQuestionIds);
            }
        }

        if (count($questionIds) == 0) {
            throw new \RuntimeException("No Questions on Paper: {$paper->id}");
        }

        $questionRepo = new ExamQuestionRepo();

        $questions = $questionRepo->findByIdsWithModelOrder($questionIds);

        $paperScore = 0;

        foreach ($questions as $question) {
            $paperScore += $question->score;
        }

        $paperUser->paper_score = $paperScore;

        $paperUser->update();

        $this->createQuestionUsers($paperUser, $questions);
    }

    /**
     * @param $allQuestions ExamQuestionModel[]
     * @param $condition array
     * @return array
     */
    protected function getRandQuestionIds($allQuestions, $condition)
    {
        $questionIds = [];

        foreach ($allQuestions as $question) {
            $case1 = $question->parent_id == 0;
            $case2 = $question->model == $condition['model'];
            $case3 = empty($condition['level']) || in_array($question->level, $condition['level']);
            if ($case1 && $case2 && $case3) {
                $questionIds[] = $question->id;
            }
        }

        $result = [];

        if (count($questionIds) > 0) {
            $randQuestionIds = kg_array_rand($questionIds, $condition['limit']);
            $result = is_array($randQuestionIds) ? $randQuestionIds : [$randQuestionIds];
        }

        return $result;
    }

    /**
     * @param $paperUser ExamPaperUserModel
     * @param $questions ExamQuestionModel[]
     */
    protected function createQuestionUsers($paperUser, $questions)
    {
        $rows = [];

        $createTime = time();

        $questionRepo = new ExamQuestionRepo();

        foreach ($questions as $question) {
            $rows[] = [
                'paper_user_id' => $paperUser->id,
                'paper_id' => $paperUser->paper_id,
                'user_id' => $paperUser->user_id,
                'question_id' => $question->id,
                'question_parent_id' => 0,
                'question_model' => $question->model,
                'question_score' => $question->score,
                'question_duration' => $question->duration,
                'create_time' => $createTime,
            ];
            if ($question->model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
                $childQuestions = $questionRepo->findChildQuestions($question->id);
                if ($childQuestions->count() > 0) {
                    foreach ($childQuestions as $childQuestion) {
                        $rows[] = [
                            'paper_user_id' => $paperUser->id,
                            'paper_id' => $paperUser->paper_id,
                            'user_id' => $paperUser->user_id,
                            'question_id' => $childQuestion->id,
                            'question_parent_id' => $childQuestion->parent_id,
                            'question_model' => $childQuestion->model,
                            'question_score' => $childQuestion->score,
                            'question_duration' => $childQuestion->duration,
                            'create_time' => $createTime,
                        ];
                    }
                }
            }
        }

        $questionUser = new ExamQuestionUserModel();

        $sql = kg_batch_insert_sql($questionUser->getSource(), $rows);

        $this->db->execute($sql);
    }

    protected function startPaperUser(ExamPaperUserModel $paperUser)
    {
        $paperUser->start_time = time();

        $paperUser->status = ExamPaperUserModel::STATUS_ACTIVE;

        $paperUser->update();
    }

}
