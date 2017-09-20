<?php

use \Faker\Factory as Faker;

/**
 * CustomDataProviders is a trait with data providers for Testing
 */
trait CustomDataProviders
{
    public function providerUserData()
    {
        $faker = Faker::create();
        $data = [
            'name' => $faker->firstName,
            'surname' => $faker->lastName,
            'email' => $faker->email,
            'phone_no' => $faker->phoneNumber,
            'password' => $faker->password,
            'tax_number' => $faker->vat,
            'thoroughfare' => $faker->streetName,
            'premise' => $faker->buildingNumber,
            'postal_code' => $faker->postcode,
            'city' => $faker->city,
            'country_code' => $faker->countryCode,
            'account_no' => $faker->iban($faker->countryCode),
            'terms_acceptance' => 'on',
        ];
        return [
            ["data" => $data]
        ];
    }
}
