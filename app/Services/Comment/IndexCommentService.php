<?php

namespace App\Services\Comment;

use App\Models\Comment;

class IndexCommentService
{
    /**
     * @var Comment
     */
    private $comment;

    /**
     * IndexCommentService constructor.
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($order_id)
    {
        return $this->comment->with('attachments', 'user')->where('order_id', $order_id)->get();
    }
}
