<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanSaveCreditCardsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    function a_user_can_add_a_valid_credit_card_to_his_account()
    {
        // Given we have a logged in user
        $user = factory(User::class)->create();
        
        // When we POST to 
        $response = $this->actingAs($user)
                         ->json('POST', '/account/billing', [
                            'number' => '4111111111111111',
                            'holder' => 'Gerhard Theunissen',
                            'expiryMonth' => '05',
                            'expiryYear' => '2017',
                            'cvv' => '123'
                        ])
                        ->assertStatus(200);
    }
}
