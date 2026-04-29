<?php

namespace App\Services\Comment;

use App\Models\Comment;
use App\Models\Order;
use App\Models\Upload;

class StoreCommentService
{
    /**
     * @var Comment
     */
    private $comment;

    /**
     * StoreCommentService constructor.
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($order_id, $data)
    {
        $order = Order::findOrFail($order_id);
        $data['user_id'] = auth()->user()->id;
        $comment = $order->comments()->create($data);

        $attachments = $data['attachments'] ?? [];
        foreach($attachments as $attachment){
            if (isset($attachment) && !empty($attachment)) {
                $fileName = $attachment->getClientOriginalName();
                $file = $attachment->storeAs('public/comment_attachments/' . $comment->id, $fileName, 'public');

                $upload = new Upload([
                    'file_name' => $file
                ]);

                $comment->uploads()->save($upload);
            }
        }

        return $comment;
    }
}
