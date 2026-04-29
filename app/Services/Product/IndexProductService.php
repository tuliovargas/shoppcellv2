<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class IndexProductService
{
    /**
     * @var Product
     */
    private $product;

    /**
     * IndexProductService constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        if (isset($request['lowStock'])) {
            return $this->getLowStock();
        }
        $search = isset($request['search']) ? $request['search'] : '';
        $category = isset($request['category']) ? $request['category'] : null;
        $product_id = isset($request['product_id']) ? $request['product_id'] : null;

        $query = $this->product->with(['categories', 'brand', 'brandModel'])
            ->when($category == 1, function ($query) {
                return $query->where(function ($query) {
                    return $query->whereHas('categories', function (Builder $query) {
                        $query->where('categories.id', '=', 1); //Exibe produtos que a categoria é Peças Reposição E Manutenção
                    });
                });
            })
            ->when($category && $category != 1, function ($query) use ($category) {
                return $query->where(function ($query) use ($category) {
                    $query->whereDoesntHave('categories')
                        ->orWhereHas('categories', function (Builder $query) use ($category) {
                            if (intval($category) < 0) {
                                return $query->where('categories.id', '!=', abs(intval($category))); //Exibe produtos que NÃO esteja categoria especificada na request
                            }
                        });
                });
            })
            ->when($category === null, function ($query) {
                return $query->where(function ($query) {
                    return $query->whereHas('categories', function (Builder $query) {
                        $query->where('categories.id', '!=', 1); //Exibe produtos que a categoria NÂO è Peças Reposição E Manutenção
                    });
                });
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    return $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%');
                });
            })
            ->when($product_id, function ($query, $product_id) {
                return $query->where(function ($q) use ($product_id) {
                    return $q->get()->last();
                });
            });

        if ($request->paginate === 'false') {
            return $query->get();
        }

        return $query->paginate(10);
    }

    public function getLowStock()
    {
        $query = $this->product->whereRaw('quantity_in_stock < minimum_stock');
        return [
            'count' => $query->count(),
            'products' => $query->limit(3)->orderBy('quantity_in_stock', 'ASC')->get()
        ];
    }
}
