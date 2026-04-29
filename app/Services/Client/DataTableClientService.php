<?php

namespace App\Services\Client;

use App\Models\Client;
use Yajra\DataTables\DataTables;
use DB;


class DataTableClientService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function run()
    {
        $this->client = $this->client
            ->select(
                'id',
                'photo_url',
                'full_name',
                'email',
                'cpf',
                'birthdate',
                'cellphone',
                'is_active',
                DB::raw('(select count(*) from orders where orders.client_id = clients.id) as total_purchases')
            );

        return Datatables::of($this->client)
            ->filterColumn('client', function ($query, $keyword) {
                $sql = "`full_name` like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addIndexColumn()
            ->addColumn('client', function ($row) {
                $photo_url = $row->photo_url ? asset('storage/' . $row->photo_url) : asset('images/default-avatar.png');
                $photo = '<img class="img-circle img-size-32" src="' . $photo_url . '">';
                return $photo . '<span class="ml-2">' . $row->full_name . '</span>';
            })
            ->addColumn('action', function ($row) {
                $editButton = "<a href=\"" . route('clients.edit', ['client' => $row->id]) . "\"><i class=\"mx-2 fas fa-pen\"></i></a>";
                $deleteButton = "<a href=\"" . route('clients.delete', ['client' => $row->id]) . "\"><i class=\"mx-2 fas fa-trash\"></i></a>";
                return $editButton . $deleteButton;
            })
            ->addColumn('total_purchases', function ($row) {
                return '<a href="' . route('orders.index', ['client_id' => $row->id]) . '"> ' . $row->total_purchases . ' </a>';
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active ? 'Ativo' : 'Inativo';
            })
            ->rawColumns(['action', 'client', 'total_purchases'])
            ->make(true);
    }
}
