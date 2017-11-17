<?php

namespace App\Billing;

use Rebilly\Entities\Customer;
use Rebilly\Entities\Plan;
use Rebilly\Entities\Subscription;
use Rebilly\Entities\Website;

class Subscriber
{
    /** @var \Rebilly\Client */
    protected $client;

    /** @var  Customer */
    protected $customer;

    /** @var  Plan */
    protected $plan;

    /** @var  Website */
    protected $website;

    public function __construct(Website $website, Plan $plan, Customer $customer)
    {
        $this->client = app(\Rebilly\Client::class);
        $this->website = $website;
        $this->plan = $plan;
        $this->customer = $customer;
    }

    /**
     * Create a subscription with all requirements set.
     *
     * @return Subscription
     * @throws \Rebilly\Http\Exception\UnprocessableEntityException
     */
    public function subscribe()
    {
        $subscriptionForm = new Subscription();
        $subscriptionForm->setCustomerId($this->customer->getId());
        $subscriptionForm->setWebsiteId($this->website->getId());
        $subscriptionForm->setPlanId($this->plan->getId());
        $address = $this->customer->getPrimaryAddress();
        $subscriptionForm->setBillingAddress($address);

        return $this->client->subscriptions()->create($subscriptionForm);
    }
}
