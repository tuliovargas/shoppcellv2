<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductLog;
use App\Models\ProductStockLog;

class ProductObserver
{
	/**
	 * Handle the Product "created" event.
	 *
	 * @param \App\Models\Product $product
	 * @return void
	 */
	public function created(Product $product)
	{
		//
	}

	/**
	 * Handle the Product "updated" event.
	 *
	 * @param \App\Models\Product $product
	 * @return void
	 */

	public function updated(Product $product)
	{
		if ($product->isDirty()) {
			$changes = $product->getChanges();
			$new = [];
			$old = [];
			foreach ($changes as $key => $change) {
				if ($key != "updated_at") {
					$new[$key] = $change;
					$old[$key] = $product->getOriginal($key);
					if ($key == 'quantity_in_stock'){
						ProductStockLog::create([
							'new' => $new[$key],
							'old' =>  $old[$key],
							'product_id' => $product->id,
							'user_id' => auth()->id(),
						]);
					}
				}
			}

			ProductLog::create([
				'new' => $new,
				'old' => $old,
				'product_id' => $product->id,
				'user_id' => auth()->id(),
			]);
		}
	}

	/**
	 * Handle the Product "deleted" event.
	 *
	 * @param \App\Models\Product $product
	 * @return void
	 */
	public function deleted(Product $product)
	{
		//
	}

	/**
	 * Handle the Product "restored" event.
	 *
	 * @param \App\Models\Product $product
	 * @return void
	 */
	public function restored(Product $product)
	{
		//
	}

	/**
	 * Handle the Product "force deleted" event.
	 *
	 * @param \App\Models\Product $product
	 * @return void
	 */
	public function forceDeleted(Product $product)
	{
		//
	}
}
