<?php declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\CartBridge\Test\Validator\Rule;

use PHPUnit\Framework\TestCase;
use Shopware\Api\Country\Struct\CountryBasicStruct;
use Shopware\Api\Customer\Struct\CustomerAddressBasicStruct;
use Shopware\Api\Customer\Struct\CustomerBasicStruct;
use Shopware\Cart\Cart\Struct\CalculatedCart;
use Shopware\CartBridge\Rule\BillingCountryRule;
use Shopware\Context\Struct\StorefrontContext;
use Shopware\Framework\Struct\StructCollection;

class BillingCountryRuleTest extends TestCase
{
    public function testWithExactMatch(): void
    {
        $rule = new BillingCountryRule(['SWAG-AREA-COUNTRY-ID-1']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(StorefrontContext::class);

        $country = new CountryBasicStruct();
        $country->setId('SWAG-AREA-COUNTRY-ID-1');
        $country->setAreaId('SWAG-AREA-ID-1');

        $billing = new CustomerAddressBasicStruct();
        $billing->setCountry($country);

        $customer = new CustomerBasicStruct();
        $customer->setDefaultBillingAddress($billing);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testWithNotMatch(): void
    {
        $rule = new BillingCountryRule(['SWAG-AREA-COUNTRY-ID-2']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(StorefrontContext::class);

        $country = new CountryBasicStruct();
        $country->setId('SWAG-AREA-COUNTRY-ID-1');
        $country->setAreaId('SWAG-AREA-ID-1');

        $billing = new CustomerAddressBasicStruct();
        $billing->setCountry($country);

        $customer = new CustomerBasicStruct();
        $customer->setDefaultBillingAddress($billing);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testMultipleCountries(): void
    {
        $rule = new BillingCountryRule(['SWAG-AREA-COUNTRY-ID-1', 'SWAG-AREA-COUNTRY-ID-3', 'SWAG-AREA-COUNTRY-ID-2']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(StorefrontContext::class);

        $country = new CountryBasicStruct();
        $country->setId('SWAG-AREA-COUNTRY-ID-1');
        $country->setAreaId('SWAG-AREA-ID-1');

        $billing = new CustomerAddressBasicStruct();
        $billing->setCountry($country);

        $customer = new CustomerBasicStruct();
        $customer->setDefaultBillingAddress($billing);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testWithoutCustomer(): void
    {
        $rule = new BillingCountryRule(['SWAG-AREA-COUNTRY-ID-1', 'SWAG-AREA-COUNTRY-ID-3', 'SWAG-AREA-COUNTRY-ID-2']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(StorefrontContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue(null));

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }
}
