<?php

namespace App\Billing;

use Rebilly\Entities\Customer;
use Rebilly\Entities\Plan;
use Rebilly\Entities\Subscription;
use Rebilly\Entities\SubscriptionCancel;
use Rebilly\Entities\Website;
use Rebilly\Http\Exception\UnprocessableEntityException;
use Rebilly\Rest\Collection;

/**
 * Subscription class.
 */
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
     * Delete all existing Subscriptions for this Customer.
     */
    protected function cancelExistingSubscriptinos()
    {
        $subscriptions = $this->fetchAllActiveCustomerSubscriptions();
        $it = $subscriptions->getIterator();
        while ($it->valid()) {
            /** @var Subscription $subscription */
            $subscription = $it->current();
            $this->cancelSubscription($subscription->getId());
            $it->next();
        }
    }

    /**
     * Delete an existing Subscription.
     *
     * @param string $subscriptionId
     *
     * @return Subscription
     * @throws UnprocessableEntityException
     */
    protected function cancelSubscription(string $subscriptionId)
    {
        $subscriptionCancelForm = new SubscriptionCancel();
        $subscriptionCancelForm->setPolicy($subscriptionCancelForm::NOW);
        $subscriptionCancelForm->setCancelCategory($subscriptionCancelForm::CATEGORY_DID_NOT_USE);
        $subscriptionCancelForm->setCanceledBy($subscriptionCancelForm::SOURCE_MERCHANT);
        $subscriptionCancelForm->setCancelDescription('Canceled programmatically, part of example code.');

        return $this->client->subscriptions()->cancel($subscriptionId, $subscriptionCancelForm);
    }

    /**
     * @return Subscription[]|Collection
     */
    protected function fetchAllActiveCustomerSubscriptions()
    {
        $subscriptions = $this->client->subscriptions()->search([
            'filter' => "customerId:{$this->customer->getId()},status:Active",
        ]);

        $filteredSubscriptions = [];
        $filteredSubscriptionCollection = new Collection(new Subscription());
        foreach ($subscriptions as $subscription) {
            if ($subscription->getStatus() == 'Active') {
                $filteredSubscriptions[] = $subscription->jsonSerialize();
            }
        }

        $filteredSubscriptionCollection->populate($filteredSubscriptions);

        return $filteredSubscriptionCollection;
    }

    /**
     * Create a subscription with all requirements set.
     *
     * @return Subscription
     * @throws \Rebilly\Http\Exception\UnprocessableEntityException
     */
    public function subscribe()
    {
        // First, for this example, we'll cancel all existing Subscriptions for this Customer. No real reason why.
        // @todo Determine if we should actually do this, might as well just update existing ones? Erm.. hmm. Think on it.
        $this->cancelExistingSubscriptinos();

        $subscriptionForm = new Subscription();
        $subscriptionForm->setCustomerId($this->customer->getId());
        $subscriptionForm->setWebsiteId($this->website->getId());
        $subscriptionForm->setPlanId($this->plan->getId());
        $address = $this->customer->getPrimaryAddress();
        $subscriptionForm->setBillingAddress($address);

        return $this->client->subscriptions()->create($subscriptionForm);
    }
}
