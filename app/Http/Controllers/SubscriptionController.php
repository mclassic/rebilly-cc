<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rebilly\Client;

class SubscriptionController extends Controller
{
    /** @var  Client */
    protected $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function redirectToCheckout(Request $request)
    {
        if ($request->input('plan') == 'toronto') {
            return redirect('https://checkout-sandbox.rebilly.com/checkout/toronto');
        } else if ($request->input('plan') == 'montreal') {
            return redirect('https://checkout-sandbox.rebilly.com/checkout/montreal');
        }
    }

    public function subscribe(Request $request)
    {
        var_dump($request->all());

        print "\n\n<br><br>\n\n";
        //var_dump($this->client);

        $website = $this->client->websites()->search('ngrok')->offsetGet(0);
        var_dump($website->getUrl());
        // var_dump($website);
    }
}
