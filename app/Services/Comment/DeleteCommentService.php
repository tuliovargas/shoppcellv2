<?php

namespace App\Services\Comment;

class DeleteCommentService
{
    /**
     * @param $comment
     * @return mixed
     */
    public function run($comment)
    {
        return $comment->delete();
    }
}
