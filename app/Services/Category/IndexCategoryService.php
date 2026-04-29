<?php

namespace App\Services\Category;

use App\Models\Category;

class IndexCategoryService
{
    /**
     * @var Category
     */
    private $category;

    /**
     * IndexClientService constructor.
     * @param Category $category
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $noSubcategories = isset($request['noSubcategories']) ? true : false;

        $query = $this->model->query();

        if($noSubcategories){
            $query = $query->whereNull('parent_id');
        }

        $query = $query->with(['children'])->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });

        return $query->get();
    }
}
