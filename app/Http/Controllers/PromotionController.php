<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FormatTrait;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\Promotion\PromotionService;
use App\Models\Configuration;
use Carbon\Carbon;

class PromotionController extends Controller
{
    use FormatTrait;

    /**
     * @var PromotionService
     */
    private $promotionService;

    /**
     * PromotionController constructor.
     * @param PromotionService $promotionService
     */
    public function __construct(
        PromotionService $promotionService
    ) {
        $this->promotionService = $promotionService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request, $type = null)
    {
        $clients = [];
        $message = '';
        $period_birthdays = $request->period_birthdays ?? 'day';
        $period_buyers = $request->period_buyers ?? 'week';

        switch($type){
            case 'week-buyers':
                $clients = $this->promotionService->getWeekBuyers($period_buyers);
                $message = Configuration::where('key', 'msg_week-buyers')->first()->value;
                break;
            case 'birthdays':
                $clients = $this->promotionService->getBirthdays($period_birthdays);
                $message = Configuration::where('key', 'msg_birthdays')->first()->value;
                break;
            case 'detached':
                $clients = $this->promotionService->getDetached();
                $message = Configuration::where('key', 'msg_detached')->first()->value;
                break;
            default:
                $clients = $this->promotionService->getWeekBuyers($period_buyers);
                $message = Configuration::where('key', 'msg_week-buyers')->first()->value;
        }

        $countWeekBuyers = $this->promotionService->countWeekBuyers($period_buyers);
        $countBirthdays = $this->promotionService->countBirthdays($period_birthdays);
        $countWeekDetached = $this->promotionService->countDetached();

        return view('promotions.index')
            ->with('clients', $clients)
            ->with('message', $message)
            ->with('countWeekBuyers', $countWeekBuyers)
            ->with('countBirthdays', $countBirthdays)
            ->with('countWeekDetached', $countWeekDetached)
            ->with('type', $type)
            ->with('period_birthdays', $period_birthdays)
            ->with('period_buyers', $period_buyers);
    }
}
