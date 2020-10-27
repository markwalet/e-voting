<?php

declare(strict_types=1);

namespace App\Services\Traits;

use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

/**
 * Validates phone numbers and returns them in a predictable way
 */
trait ValidatesPhoneNumbers
{
    /**
     * Validates the number and if it's valid, returns it in the given format.
     * @param string $phone
     * @param string $country Country code for the dialing country
     * @param null|array $validTypes List of PhoneNumberType constants
     * @param int $format PhoneNumberFormat constant
     * @return null|string
     */
    protected function formatPhoneNumber(
        string $phone,
        string $country = 'NL',
        ?array $validTypes = null,
        int $format = PhoneNumberFormat::E164
    ): ?string {
        // Skip if empty
        if (empty($phone)) {
            return null;
        }

        // Replace non-digits and plus signs not at the start
        $phone = \preg_replace('/[^0-9\+]+|(?<!^)\+/', '', $phone);

        // Ensure a zero at the start
        if (!Str::startsWith($phone, ['+', '0'])) {
            $phone = "0{$phone}";
        }

        // Assign types
        $validTypes ??= [
            PhoneNumberType::FIXED_LINE_OR_MOBILE,
            PhoneNumberType::FIXED_LINE,
            PhoneNumberType::MOBILE,
            PhoneNumberType::PERSONAL_NUMBER,
            PhoneNumberType::UAN,
            PhoneNumberType::UNKNOWN,
            PhoneNumberType::VOIP,
        ];

        // Get instance
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse
            $phoneObject = $phoneUtil->parse($phone, $country);

            if (
                // Validate phone
                !$phoneUtil->isValidNumber($phoneObject) ||
                // Validate region
                !$phoneUtil->isValidNumberForRegion($phoneObject, $country) ||
                // Validate type
                !\in_array($phoneUtil->getNumberType($phoneObject), $validTypes)
            ) {
                return null;
            }

            // Return as formatted number
            return $phoneUtil->format($phoneObject, $format);
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
