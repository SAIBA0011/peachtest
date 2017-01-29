<?php

namespace App\Http\Controllers;

use App\Card;
use App\Peach;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCreditCardRequest;

class BillingController extends Controller
{
	private $peach;

	public function __construct(Peach $peach)
	{
		$this->peach = $peach;
	}

    public function index()
    {
    	return view('account.billing');
    }

    public function store(StoreCreditCardRequest $request)
    {
    	$auth = $this->peach->autorize(
    		$request->number,
    		$request->holder,
    		$request->exp_month,
    		$request->exp_year,
    		$request->cvv,
    		'/account/billing/return'
		);

		return response()->json($auth);
    }

    public function primary()
    {
    	auth()->user()->update([
    		'primary_card' => request()->id
		]);
    }

    public function remove()
    {
    	$card = Card::find(request()->card_id);
    	$this->peach->deleteToken($card->token);
    	$card->delete();
    }

    public function return()
    {
    	if(! request()->id) {
    		return redirect()->route('account.billing.index');
    	}

    	$payment = $this->peach->fetchPayment(request()->id);

    	if(Card::where('token', $payment->registrationId)->exists()) {

    		// Flash Error - Credit Card already linked with another account, please use a different credit card or contact us for support.
    		return redirect()->route('account.billing.index');
    	}

    	if($payment->successful()) {
    		$card = new Card([
    			'token' => $payment->registrationId,
    			'brand' => $payment->paymentBrand,
    			'number' => $payment->card['bin'] . '****' . $payment->card['last4Digits'],
    			'exp_month' => $payment->card['expiryMonth'],
    			'exp_year' => $payment->card['expiryYear']
			]);

    		auth()->user()->cards()->save($card);

    		if(count(auth()->user()->cards) == 1) {
    			auth()->user()->update([
    				'primary_card' => $card->id
				]);
    		}

    		// Add Flash Success
			return redirect()->route('account.billing.index');
    	}

    	// Add Flash Failure
		return redirect()->route('account.billing.index');
    }
}
