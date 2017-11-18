<?php

namespace App\Http\Controllers;

use App\Billing\CustomerRepository;
use App\Billing\Subscriber;
use Illuminate\Http\Request;
use Rebilly\Client;
use Rebilly\Http\Exception\UnprocessableEntityException;

class SubscriptionController extends Controller
{
    /** @var  Client */
    protected $client;

    public function __construct(Client $client)
    {
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

    public function showSubscriptionForm()
    {
        $signature = \Rebilly\Util\RebillySignature::generateSignature(
            env('REBILLY_API_USER'),
            env('REBILLY_API_SECRET')
        );

        return view('checkout', [
            'signature' => $signature,
        ]);
    }

    public function subscribe(Request $request)
    {
        // var_dump($request->all());
        // die;

        // $paymentToken = $this->client->paymentCardTokens()->load($request->input('payment-token'));
        // var_dump($paymentToken); die;

        //print "\n\n<br><br>\n\n";
        //var_dump($this->client);

        //$website = $this->client->websites()->search('ngrok')->offsetGet(0);
        //var_dump($website->getUrl());
        // var_dump($website);

        // var_dump($this->client->checkoutPages()->load('751922d0-177c-477c-a15a-fb8333412b7f'));

        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $address1 = $request->input('address1');
        $address2 = $request->input('address2');
        $city = $request->input('city');
        $region = $request->input('region');
        $postalCode = $request->input('postalcode');
        $country = $request->input('country');
        $phoneNumber = $request->input('phoneNumber');
        $data = [
            'primaryAddress' => [
                'firstName'    => $firstName,
                'lastName'     => $lastName,
                'address'      => $address1,
                'address2'     => $address2,
                'city'         => $city,
                'region'       => $region,
                'country'      => $country,
                'postalCode'   => $postalCode,
                'emails'       => [
                    [
                        'label'   => 'main',
                        'value'   => $email,
                        'primary' => true,
                    ],
                ],
                'phoneNumbers' => [
                    [
                        'label'   => 'main',
                        'value'   => $phoneNumber,
                        'primary' => true,
                    ],
                ],
            ],
        ];

        // FLOW (Subject to change):
        // 1. Create Customer
        // 2. Create Default Payment Method
        // 3. Load website
        // 4. Load Plan
        // 5. Create Subscription
        try {
            $paymentTokenId = $request->input('payment-token');
            $customer = CustomerRepository::create($data, $paymentTokenId);

            $website = $this->client->websites()->load(env('REBILLY_WEBSITE_ID'));
            if ($request->input('plan') == 'montreal') {
                $planId = env('REBILLY_PLAN_MONTREAL_ID');
            } else if ($request->input('plan') == 'toronto') {
                $planId = env('REBILLY_PLAN_TORONTO_ID');
            }

            if (empty($planId)) {
                print "<h1>Invalid Plan</h1><br>\n\n";

                return;
            }

            $plan = $this->client->plans()->load($planId);
            $subscriber = new Subscriber($website, $plan, $customer);
            $subscription = $subscriber->subscribe();
            var_dump($subscription);
        } catch (UnprocessableEntityException $e) {
            print "<h1>Whoops!</h1><br>\n\n";
            print $e->getErrors()[0];
        }

        // return $this->client->checkoutPages()->load('751922d0-177c-477c-a15a-fb8333412b7f')->getRedirectUrl();
    }
}
