<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\IndexCommentRequest;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Models\Comment;
use App\Services\Comment\DeleteCommentService;
use App\Services\Comment\IndexCommentService;
use App\Services\Comment\StoreCommentService;
use App\Services\Upload\DeleteUploadService;
use App\Services\Upload\StoreUploadService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderCommentController extends Controller
{

    /**
     * @var IndexCommentService
     */
    private $indexCommentService;

    /**
     * @var StoreCommentService
     */
    private $storeCommentService;

    /**
     * @var DeleteCommentService
     */
    private $deleteCommentService;

    /**
     * @var StoreUploadService
     */
    private $storeUploadService;

    /**
     * @var DeleteUploadService
     */
    private $deleteUploadService;

    /**
     * CommentController constructor.
     * @param IndexCommentService $indexCommentService
     * @param StoreCommentService $storeCommentService
     * @param DeleteCommentService $deleteCommentService
     * @param StoreUploadService $storeUploadService
     * @param DeleteUploadService $deleteUploadService
     */
    public function __construct(
        IndexCommentService $indexCommentService,
        StoreCommentService $storeCommentService,
        DeleteCommentService $deleteCommentService,
        StoreUploadService $storeUploadService,
        DeleteUploadService $deleteUploadService
    ) {
        $this->indexCommentService = $indexCommentService;
        $this->storeCommentService = $storeCommentService;
        $this->deleteCommentService = $deleteCommentService;
        $this->storeUploadService = $storeUploadService;
        $this->deleteUploadService = $deleteUploadService;
    }

    /**
     * @param IndexCommentRequest $indexCommentRequest
     * @return Application|ResponseFactory|Response
     */
    public function index($order_id)
    {
        $comments = $this->indexCommentService->run($order_id);
        return response()->json($comments);
    }

    /**
     * @param StoreCommentRequest $storeCommentRequest
     * @return RedirectResponse
     */
    public function store($order_id, Request $request)
    {
        $comment = $this->storeCommentService->run($order_id, $request->all());
        return response()->json($comment);
    }

    /**
     * @param Comment $coupon
     * @return Application|ResponseFactory|Response
     */
    public function destroy(Comment $coupon)
    {
        $this->deleteCommentService->run($coupon);
        return response()->json($coupon);
    }
}
