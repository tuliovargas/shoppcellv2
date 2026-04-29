<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderUpload\IndexOrderUploadRequest;
use App\Http\Requests\OrderUpload\StoreOrderUploadRequest;
use App\Models\OrderUpload;
use App\Models\Upload;
use App\Services\OrderUpload\DeleteOrderUploadService;
use App\Services\OrderUpload\IndexOrderUploadService;
use App\Services\OrderUpload\StoreOrderUploadService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderUploadController extends Controller
{

    /**
     * @var IndexOrderUploadService
     */
    private $indexOrderUploadService;

    /**
     * @var StoreOrderUploadService
     */
    private $storeOrderUploadService;

    /**
     * @var DeleteOrderUploadService
     */
    private $deleteOrderUploadService;

    /**
     * OrderUploadController constructor.
     * @param IndexOrderUploadService $indexOrderUploadService
     * @param StoreOrderUploadService $storeOrderUploadService
     * @param DeleteOrderUploadService $deleteOrderUploadService
     */
    public function __construct(
        IndexOrderUploadService $indexOrderUploadService,
        StoreOrderUploadService $storeOrderUploadService,
        DeleteOrderUploadService $deleteOrderUploadService
    ) {
        $this->indexOrderUploadService = $indexOrderUploadService;
        $this->storeOrderUploadService = $storeOrderUploadService;
        $this->deleteOrderUploadService = $deleteOrderUploadService;
    }

    /**
     * @param IndexOrderUploadRequest $indexOrderUploadRequest
     * @return Application|ResponseFactory|Response
     */
    public function index($order_id)
    {
        $comments = $this->indexOrderUploadService->run($order_id);
        return response()->json($comments);
    }

    /**
     * @param StoreOrderUploadRequest $storeOrderUploadRequest
     * @return RedirectResponse
     */
    public function store($order_id, Request $request)
    {
        $comment = $this->storeOrderUploadService->run($order_id, $request->all());
        return response()->json($comment);
    }

    /**
     * @param OrderUpload $coupon
     * @return Application|ResponseFactory|Response
     */
    public function destroy(Upload $upload)
    {
        $this->deleteOrderUploadService->run($upload);
        return response()->json($upload);
    }
}
