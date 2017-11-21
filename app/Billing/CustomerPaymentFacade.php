<?php

namespace App\Billing;

use Rebilly\Client;
use Rebilly\Entities\Customer;
use Rebilly\Entities\PaymentCardToken;
use Rebilly\Entities\PaymentInstruments\PaymentCardInstrument;

/**
 * Facade class that provides a basic API and bundles together any business logic necessary in order to facilitate
 * payment cards, instruments with payment token for a provided Customer.
 */
class CustomerPaymentFacade
{
    /** @var  Client */
    protected $client;

    /** @var  Customer */
    protected $customer;

    /** @var  array */
    protected $data;

    /** @var  PaymentCardToken */
    protected $paymentCardToken;

    public function __construct(Customer $customer, PaymentCardToken $paymentCardToken)
    {
        $this->client = app(Client::class);
        $this->customer = $customer;
        $this->paymentCardToken = $paymentCardToken;
        $this->data = $this->customer->jsonSerialize();
    }

    public function updateCustomer()
    {
        return $this->client->customers()->update($this->customer->getId(), $this->customer->jsonSerialize());
    }

    public function setDefaultPaymentMethodInstrument()
    {
        /*
         * Andrei Moldoveanu [9 minutes ago]
         * I think it would be a multi-step process:
         * - create payment card from token
         * - attribute defaultPaymentInstrument using new card in customer
         * as far as I know you can't use the token directly
         */
        $customerData = $this->customer->jsonSerialize();
        $customerData['customerId'] = $customerData['id'];
        $paymentCard = $this->client->paymentCards()->createFromToken($this->paymentCardToken->getId(), $customerData);
        $paymentInstrument = new PaymentCardInstrument($paymentCard->jsonSerialize());
        $paymentInstrument->setPaymentCardId($paymentCard->getId());
        $this->customer->setDefaultPaymentInstrument($paymentInstrument);
    }
}
