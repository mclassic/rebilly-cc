<?php

namespace App\Billing;

use Rebilly\Entities\Customer;
use Rebilly\Http\Exception\UnprocessableEntityException;

class CustomerFactory
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
     * @param array $data
     *
     * @return Customer
     * @throws UnprocessableEntityException
     */
    public static function create(array $data)
    {
        self::setUp();
        if (isset($data['primaryAddress']['firstName'], $data['primaryAddress']['lastName'])) {
            $searchData = [
                'firstName' => $data['primaryAddress']['firstName'],
                'lastName' => $data['primaryAddress']['lastName']
            ];

            $existingCustomer = static::search($searchData);
            if ($existingCustomer instanceof Customer) {
                return $existingCustomer;
            }
        }

        $customerForm = new Customer();
        $customerForm->setPrimaryAddress($data['primaryAddress']);
        $customer = self::$client->customers()->create($customerForm);
        // $customer->setDefaultPaymentInstrument(new \Rebilly\Entities\PaymentInstruments\PaymentCardInstrument());

        return $customer;
    }

    /**
     * @param string $customerId
     *
     * @return Customer
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
