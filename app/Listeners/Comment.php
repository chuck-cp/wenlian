<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Listeners;

use App\Models\Answer as AnswerModel;
use App\Models\Comment as CommentModel;
use Phalcon\Events\Event as PhEvent;

class Comment extends Listener
{

    public function afterCreate(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterUpdate(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterDelete(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterRestore(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterApprove(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterReject(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterReply(PhEvent $event, $source, CommentModel $reply)
    {

    }

    public function afterLike(PhEvent $event, $source, CommentModel $comment)
    {

    }

    public function afterUndoLike(PhEvent $event, $source, CommentModel $comment)
    {

    }

}