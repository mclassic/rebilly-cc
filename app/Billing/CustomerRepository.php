<?php

namespace App\Billing;

use Rebilly\Client;
use Rebilly\Entities\Customer;

/**
 * Repository class that finds or loads a customer via several different methods. Also ensures that Customer is set with
 * a default payment and any other domain requirements.
 * <p />
 * Static methods are intentional as each public static method should perform its own isolated function when called from
 * outside. Although some methods do rely on other methods in this class, each method should be able to perform its
 * intentions on its own.
 */
class CustomerRepository
{
    /** @var  Client */
    protected static $client;

    /**
     * Set up any requirements that methods will need, mainly loading the Rebilly client from the app container.
     */
    protected static function setUp()
    {
        self::$client = app(Client::class);
    }

    /**
     * Create or load an existing Customer with the provided data. Will attempt to avoid creating duplicate Customers
     * by first performing a rudimentary search.
     *
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

            // @todo Review whether or not to use late static binding.
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
     * Load a Customer via its id.
     *
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
     * Perform a search for any existing Customers via the passed array of Customer properties. Return the first
     * Customer found, even if more than one is returned in the search results.
     *
     * @param array $fields
     *
     * @return Customer
     */
    public static function search(array $fields)
    {
        self::setUp();
        $customers = self::$client->customers()->search([
            'filter' => "firstName:{$fields['firstName']},lastName:{$fields['lastName']}",
        ]);

        /** @var \Rebilly\Entities\Customer $customer */
        $customer = $customers->offsetGet(0);

        return $customer;
    }
}
