<?php

namespace App\Services\BrandModel;

use App\Models\BrandModel;
use Yajra\DataTables\DataTables;


class DataTableBrandModelService
{
    private $brandModel;

    public function __construct(BrandModel $brandModel)
    {
        $this->brandModel = $brandModel;
    }

    public function run()
    {
        $model = $this->brandModel
            ->with(['brand'])
            ->select(
                'id',
                'photo_url',
                'name',
                'brand_id',
            );

        return Datatables::of($model)
            ->addIndexColumn()
            ->editColumn('photo_url', function ($row) {
                $photo_url = $row->photo_url ? asset('storage/' . $row->photo_url) : asset('images/default-image.png');
                return '<img class="img-size-32" src="' . $photo_url . '"/>';
            })
            ->editColumn('brand', function ($row) {
                if (!$row->brand) return '';
                return $row->brand->name;
            })
            ->addColumn('action', function ($row) {
                $editButton = "<a href=\"" . route('brand-models.edit', $row->id) . "\"><i class=\"mx-2 fas fa-pen\"></i></a>";
                $deleteButton = "<a href=\"" . route('brand-models.delete', ['brand_model' => $row->id]) . "\"><i class=\"mx-2 fas fa-trash\"></i></a>";
                return $editButton . $deleteButton;
            })
            ->rawColumns(['photo_url', 'action'])
            ->make(true);
    }
}
