<?php

namespace App\Http\Controllers;

use App\Peach;
use App\Product;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
	private $peach;

	public function __construct(Peach $peach)
	{
		$this->peach = $peach;
	}

    public function store()
    {
    	$product = Product::find(request()->product_id);

    	$data = $this->peach->charge(
    		auth()->user()->primaryCard->token, 
    		$product->price / 100,
            "Product: {$product->id}",
            "Purchased Product {$product->id}"
		);

    	dd($data);

		return redirect()->route('account.billing.index');
    }
}
