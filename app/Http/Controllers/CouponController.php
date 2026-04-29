<?php

namespace App\Http\Controllers;

use App\Exceptions\DeleteNotAllowedException;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\Coupon\DataTableCouponService;
use App\Services\Coupon\DeleteCouponService;
use App\Services\Coupon\IndexCouponService;
use App\Services\Coupon\StoreCouponService;
use App\Services\Coupon\UpdateCouponService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CouponController extends Controller
{

    /**
     * @var IndexCouponService
     */
    private $indexCouponService;

    /**
     * @var StoreCouponService
     */
    private $storeCouponService;

    /**
     * @var UpdateCouponService
     */
    private $updateCouponService;

    /**
     * @var DeleteCouponService
     */
    private $deleteCouponService;

    /**
     * CouponController constructor.
     * @param IndexCouponService $indexCouponService
     * @param StoreCouponService $storeCouponService
     * @param UpdateCouponService $updateCouponService
     * @param DeleteCouponService $deleteCouponService
     */
    public function __construct(
        IndexCouponService $indexCouponService,
        StoreCouponService $storeCouponService,
        UpdateCouponService $updateCouponService,
        DeleteCouponService $deleteCouponService
    ) {
        $this->indexCouponService = $indexCouponService;
        $this->storeCouponService = $storeCouponService;
        $this->updateCouponService = $updateCouponService;
        $this->deleteCouponService = $deleteCouponService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request, DataTableCouponService $dataTableCouponService)
    {
        if ($request->ajax() && $request->get('type') === 'datatables') {
            return $dataTableCouponService->run($request->query('client_id'));
        }

        $search = null;
        if ($request->search) {
            $search = $request->search;
        }

        $coupons = $this->indexCouponService->run($request);

        if ($request->expectsJson()) {
            return response($coupons);
        }

        return view('coupons.index')
            ->with('coupons', $coupons)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('coupons.create');
    }

    /**
     * @param StoreCouponRequest $storeCouponRequest
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $this->storeCouponService->run($data);

        return redirect()->route('coupons.index')->with([
            'success' => true,
            'message' => 'Cupom cadastrado com sucesso'
        ]);
    }

    /**
     * @param Coupon $coupon
     * @return Application|Factory|View
     */
    public function edit(Coupon $coupon)
    {
        return view('coupons.edit')
            ->with('coupon', $coupon);
    }

    /**
     * @param UpdateCouponRequest $updateCouponRequest
     * @param Coupon $coupon
     * @return RedirectResponse
     */
    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->all();
        $this->updateCouponService->run($data, $coupon);
        return redirect()->route('coupons.index')->with([
            'success' => true,
            'message' => 'Cupom editado com sucesso'
        ]);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function delete($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('coupons.delete', compact('coupon'));
    }

    /**
     * @param Coupon $coupon
     * @return Application|ResponseFactory|Response
     * @throws DeleteNotAllowedException
     */
    public function destroy(Coupon $coupon)
    {
        $this->deleteCouponService->run($coupon);
        return redirect()->route('coupons.index')->with([
            'success' => true,
            'message' => 'Cupom removido com sucesso'
        ]);
    }
}
