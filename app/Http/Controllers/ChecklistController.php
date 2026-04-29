<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checklist\StoreChecklistRequest;
use App\Http\Requests\Checklist\UpdateChecklistRequest;
use App\Models\Checklist;
use App\Services\Checklist\DeleteChecklistService;
use App\Services\Checklist\IndexChecklistService;
use App\Services\Checklist\StoreChecklistService;
use App\Services\Checklist\UpdateChecklistService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChecklistController extends Controller
{
    /**
     * @var DeleteChecklistService
     */
    private $deleteChecklistService;

    /**
     * @var UpdateChecklistService
     */
    private $updateChecklistService;

    /**
     * @var StoreChecklistService
     */
    private $storeChecklistService;

    /**
     * @var IndexChecklistService
     */
    private $indexChecklistService;

    /**
     * ChecklistController constructor.
     * @param IndexChecklistService $indexChecklistService
     * @param StoreChecklistService $storeChecklistService
     * @param UpdateChecklistService $updateChecklistService
     * @param DeleteChecklistService $deleteChecklistService
     */
    public function __construct(
        IndexChecklistService $indexChecklistService,
        StoreChecklistService $storeChecklistService,
        UpdateChecklistService $updateChecklistService,
        DeleteChecklistService $deleteChecklistService)
    {
        $this->indexChecklistService = $indexChecklistService;
        $this->storeChecklistService = $storeChecklistService;
        $this->updateChecklistService = $updateChecklistService;
        $this->deleteChecklistService = $deleteChecklistService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request)
    {
        $search = null;
        if ($request->search) {
            $search = $request->search;
        }

        $checklists = $this->indexChecklistService->run($request);

        if ($request->expectsJson()) {
            return response()->json($checklists);
        }

        return view('checklist.index')
            ->with('checklists', $checklists)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('checklist.create');
    }

    /**
     * @param StoreChecklistRequest $storeChecklistRequest
     * @return RedirectResponse
     */
    public function store(StoreChecklistRequest $storeChecklistRequest)
    {
        $data = $storeChecklistRequest->validated();
        $this->storeChecklistService->run($data);
        return redirect()->route('configurations.checklists.index');
    }

    /**
     * @param Checklist $checklist
     * @return Application|Factory|View
     */
    public function edit(Checklist $checklist)
    {
        return view('checklist.edit')
            ->with('checklist', $checklist);
    }

    /**
     * @param UpdateChecklistRequest $updateChecklistRequest
     * @param Checklist $checklist
     * @return RedirectResponse
     */
    public function update(UpdateChecklistRequest $updateChecklistRequest, Checklist $checklist)
    {
        $data = $updateChecklistRequest->validated();
        $this->updateChecklistService->run($data, $checklist);
        return redirect()->route('configurations.checklists.index');
    }

    /**
     * @param Checklist $checklist
     * @return RedirectResponse
     */
    public function delete(Checklist $checklist)
    {
        $this->deleteChecklistService->run($checklist);
        return redirect()->route('configurations.checklists.index');
    }
}
