<?php

namespace App\Services\Promotion;

use App\Models\Order;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PromotionService
{
    
    public function getWeekBuyers($period){
        $clients = Client::join('orders', 'orders.client_id', '=', 'clients.id')
            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_products.product_id');
            
        switch($period){
            case 'month':
                $clients = $clients->whereBetween('orders.created_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()]);
                break;
            default: //week
                $clients = $clients->whereBetween('orders.created_at', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()]);
                break;
        }

        return $clients
            ->where('orders.status', 'concluded')
            ->where('clients.id', '!=', 1) // consumidor final
            ->whereRaw('LENGTH(cellphone) >= 9')
            ->select('clients.*', 'products.name as product_name')
            ->orderBy('full_name')
            ->distinct()
            ->get();
    }

    public function getBirthdays($period){
        $clients = Client::where('clients.id', '!=', 1) // consumidor final
            ->whereRaw('LENGTH(cellphone) >= 9');
            
        switch($period){
            case 'day':
                $clients = $clients
                    ->whereMonth('birthdate', Carbon::today()->month)
                    ->whereDay('birthdate', Carbon::today()->day);
                break;
            case 'week':
                $clients = $clients
                    ->whereMonth('birthdate', Carbon::today()->month)
                    ->where(function ($query) {
                        $start = Carbon::today()->startOfWeek();
                        $end = Carbon::today()->endOfWeek();

                        $query->whereDay('birthdate', '>=', $start->day)
                            ->whereDay('birthdate', '<=', $end->day);
                    });
                break;
            case 'month':
                $clients = $clients->whereMonth('birthdate', Carbon::today()->month);
                break;
        }

        return $clients
                ->select('clients.*')
                ->orderBy('full_name')
                ->distinct()
                ->get();
    }

    public function getDetached(){
        return Client::join('orders', 'orders.client_id', '=', 'clients.id')
            ->where('orders.status', 'concluded')
            ->where('clients.id', '!=', 1) // consumidor final
            ->whereRaw('LENGTH(cellphone) >= 9')
            ->select('clients.*')
            ->orderBy('full_name')
            ->distinct()
            ->get();
    }

    public function countWeekBuyers($period){
        $clients = Client::join('orders', 'orders.client_id', '=', 'clients.id');
            
        switch($period){
            case 'month':
                $clients = $clients->whereBetween('orders.created_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()]);
                break;
            default: //week
                $clients = $clients->whereBetween('orders.created_at', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()]);
                break;
            
        }

        return $clients
            ->where('orders.status', 'concluded')
            ->where('clients.id', '!=', 1) // consumidor final
            ->whereRaw('LENGTH(cellphone) >= 9')
            ->select('clients.id')
            ->distinct()
            ->count();
    }

    public function countBirthdays($period){
        $clients = Client::where('clients.id', '!=', 1) // consumidor final
            ->whereRaw('LENGTH(cellphone) >= 9');
            
        switch($period){
            case 'day':
                $clients = $clients
                    ->whereMonth('birthdate', Carbon::today()->month)
                    ->whereDay('birthdate', Carbon::today()->day);
                break;
            case 'week':
                $clients = $clients
                    ->whereMonth('birthdate', Carbon::today()->month)
                    ->where(function ($query) {
                        $start = Carbon::today()->startOfWeek();
                        $end = Carbon::today()->endOfWeek();

                        $query->whereDay('birthdate', '>=', $start->day)
                            ->whereDay('birthdate', '<=', $end->day);
                    });
                break;
            case 'month':
                $clients = $clients->whereMonth('birthdate', Carbon::today()->month);
                break;
        }

        return $clients
                ->select('clients.id')
                ->distinct()
                ->count();
    }

    public function countDetached(){
        return Client::join('orders', 'orders.client_id', '=', 'clients.id')
            ->where('orders.status', 'concluded')
            ->where('clients.id', '!=', 1) // consumidor final
            ->whereRaw('LENGTH(cellphone) >= 9')
            ->select('clients.id')
            ->distinct()
            ->count();
    }
}
