<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Membership;
use App\Models\PlanType;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Support\Str;

class MembersImportService
{
    public static function importMembers($filename)
    {
        /**
         * Headers:
         *
         * Id
         * Title
         * First Name
         * Wife Name
         * Last Name
         * City
         * Address
         * Postal Code
         * Home Phone
         * Mobile Phone
         * Wife Mobile Phone
         * Shtibel
         * Hebrew First Name
         * Hebrew Last Name
         * Father
         * Father In Law
         * Email
         */
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename, true);

        while (($data = fgetcsv($handle, 1000)) !== false) {
            if (is_array($data)) {

                $member = Member::findOrNew($data[$headers->search('id')]);

                collect($headers)->each(fn($header, $key) => $member->$header = $data[$key]);

                $member->save();
            }
        }
    }

    public static function importMemberships($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        $planTypes = [
            '18'            => PlanType::firstWhere('name', '18')->id,
            'HS'            => PlanType::firstWhere('name', 'Horues Shu')->id,
            'Member'        => PlanType::firstWhere('name', 'Regular Member')->id,
            'Former Member' => PlanType::firstWhere('name', 'Regular Member')->id,
            'NEW'           => PlanType::firstWhere('name', 'New')->id,
        ];

        while (($data = fgetcsv($handle, 1000)) !== false) {
            if (is_array($data)) {

                $membership = Membership::updateOrCreate(
                    [
                        'member_id' => trim($data[$headers->search('ID')])
                    ], [
                        'type'         => Membership::TYPE_MEMBERSHIP,
                        'plan_type_id' => $planTypes[trim($data[$headers->search('Membership Type')])],
                        'is_active'    => strtolower(trim($data[$headers->search('Is Active')])) == 'yes'
                    ]
                );

                if (!empty($comments = trim($data[$headers->search('Comments')]))) {
                    $membership->comments()->create(['content' => $comments]);
                }
            }
        }
    }

    public static function importChildren($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($data = fgetcsv($handle, 1000)) !== false) {
            if (is_array($data)) {

                $member = Member::findOrFail(trim($data[$headers->search('ID')]));

                try {
                    $dob = Carbon::parse(trim($data[$headers->search('DOB')]));
                } catch (InvalidFormatException $exception) {}

                $member->dependents()->updateOrCreate([
                    'name' => trim($data[$headers->search('Name')]),
                    'dob'  => $dob ?? null,
                ], [
                    'is_married' => strtolower(trim($data[$headers->search('Is Married')])) == 'yes'
                ]);
            }
        }
    }

    public static function importLoans($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($data = fgetcsv($handle, 1000)) !== false) {
            if (is_array($data)) {

                $membership = Member::findOrFail(trim($data[$headers->search('ID')]))->membership;

                $loan = $membership->loans()->updateOrCreate([
                    'cheque_number' => trim($data[$headers->search('Check Numbers')]),
                ], [
                    'loan_date' => Carbon::parse(trim($data[$headers->search('Loan Date')])),
                    'amount'    => Str::of($data[$headers->search('Loan Amount')])->trim()->replace(['$', ','], ''),
                ]);

                $loan->guarantors()->sync(
                    collect($data)
                        ->only(array_keys($headers->filter(fn($header) => Str::startsWith($header, 'Guarantor'))->all()))
                        ->filter()
                        ->toArray()
                );

                if (!empty($paymentStart = trim($data[$headers->search('Payment Start')]))) {
                    $loan->comments()->create(['content' => 'Payments Start: ' . $paymentStart]);
                }

                if (!empty($comments = trim($data[$headers->search('Comments')]))) {
                    $loan->comments()->create(['content' => $comments]);
                }
            }
        }
    }

    private static function handle($filename)
    {
        $handle = fopen($filename, "r");

        if ($handle === false) {
            throw_if($handle === false, Exception::class, "Could not open file $filename");
        }

        return $handle;
    }

    private static function headers($handle, $filename, $snakeCase = false)
    {
        $headers = collect(fgetcsv($handle, 1000))
            ->filter()
            ->map(fn($header) => $snakeCase ? Str::snake($header) : $header);

        throw_if($headers->isEmpty(), Exception::class, "No header row in $filename file");

        return $headers;
    }
}
