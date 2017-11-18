<?php

namespace App\Billing;

use Rebilly\Entities\Customer;

class CustomerRepository
{
    /** @var  \Rebilly\Client */
    protected static $client;

    /**
     * Set up the Factory class.
     */
    protected static function setUp()
    {
        self::$client = app(\Rebilly\Client::class);
    }

    /**
     * @param array  $data
     * @param string $paymentTokenId
     *
     * @return Customer
     */
    public static function create(array $data, string $paymentTokenId)
    {
        self::setUp();
        if (isset($data['primaryAddress']['firstName'], $data['primaryAddress']['lastName'])) {
            $searchData = [
                'firstName' => $data['primaryAddress']['firstName'],
                'lastName'  => $data['primaryAddress']['lastName'],
            ];

            $existingCustomer = static::search($searchData);
            if ($existingCustomer instanceof Customer) {
                if (is_null($existingCustomer->getDefaultPaymentInstrument())) {
                    // First let's check for any existing PaymentCards attached to the customer

                    $customerPayment = new CustomerPaymentFacade(
                        $existingCustomer,
                        self::$client->paymentCardTokens()->load($paymentTokenId)
                    );

                    $customerPayment->setDefaultPaymentMethodInstrument();
                    $existingCustomer = $customerPayment->updateCustomer();
                    dd($existingCustomer);
                }

                return $existingCustomer;
            }
        }

        $customerForm = new Customer();
        $customerForm->setPrimaryAddress($data['primaryAddress']);
        $customer = self::$client->customers()->create($customerForm);
        $customerPayment = new CustomerPaymentFacade(
            $customer,
            self::$client->paymentCardTokens()->load($paymentTokenId)
        );

        $customerPayment->setDefaultPaymentMethodInstrument();
        $customer = $customerPayment->updateCustomer();

        return $customer;
    }

    /**
     * @param string $customerId
     *
     * @return Customer
     * @internal
     */
    public static function find(string $customerId)
    {
        self::setUp();

        return self::$client->customers()->load($customerId);
    }

    /**
     * @param array $fields
     *
     * @return Customer
     * @internal
     */
    public static function search(array $fields)
    {
        self::setUp();
        $customers = self::$client->customers()->search([
            'filter' => "firstName:{$fields['firstName']},lastName:{$fields['lastName']}}",
        ]);

        /** @var \Rebilly\Entities\Customer $customer */
        $customer = $customers->offsetGet(0);

        return $customer;

        /*
        if (false && $customers->count() > 1) {
            $customer = $customers->offsetGet(0);
            for ($i = ($customers->count() - 1); $i > 0; $i--) {
                    /** @var \Rebilly\Entities\Customer $currentCustomer
                    $currentCustomer = $customers->offsetGet(0);
                }
        }
        */
    }
}
