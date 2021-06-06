<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PlanType;
use App\Models\Subscription;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CsvImportService
{
    public static function importMembers($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($row = fgetcsv($handle, 1000)) !== false) {

            if (!is_array($row)) continue;

            $member = Member::findOrNew($row[$headers->search('id')]);

            collect($headers)->each(fn($header, $key) => $member->$header = $row[$key]);

            $member->save();
        }
    }

    public static function importMemberships($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        $planTypes = [
            '18'            => PlanType::firstOrCreate(['name' => '18'])->id,
            'HS'            => PlanType::firstOrCreate(['name' => 'Horues Shu'])->id,
            'Member'        => PlanType::firstOrCreate(['name' => 'Regular Member'])->id,
            'Former Member' => PlanType::firstOrCreate(['name' => 'Former Member'])->id,
        ];

        while (($row = fgetcsv($handle, 1000)) !== false) {

            if (!is_array($row)) continue;

            if (trim($row[$headers->search('Membership Type')]) == 'NEW') continue;

            Member::findOrFail(trim($row[$headers->search('ID')]))
                ->update([
                    'membership_type'   => Member::TYPE_MEMBERSHIP,
                    'plan_type_id'      => $planTypes[trim($row[$headers->search('Membership Type')])],
                    'active_membership' => strtolower(trim($row[$headers->search('Is Active')])) == 'yes'
                ]);
        }
    }

    public static function importChildren($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($row = fgetcsv($handle, 1000)) !== false) {

            if (!is_array($row)) continue;

            $member = Member::findOrFail(trim($row[$headers->search('ID')]));

            try {
                $dob = Carbon::parse(trim($row[$headers->search('DOB')]));
            } catch (InvalidFormatException $exception) {
                $dob = null;
            }

            $member->dependents()->updateOrCreate([
                'name' => trim($row[$headers->search('Name')]),
                'dob'  => $dob,
            ], [
                'is_married' => strtolower(trim($row[$headers->search('Is Married')])) == 'yes'
            ]);

        }
    }

    public static function importLoans($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($row = fgetcsv($handle, 1000)) !== false) {

            if (!is_array($row)) continue;

            $member = Member::findOrFail(trim($row[$headers->search('ID')]));

            if (!$member->membership_since) {
                Log::warning("[IMPORT LOANS (CSV)] No membership for member #$member->id creating membership...");
                $member->update([
                    'membership_type' => Member::TYPE_MEMBERSHIP,
                    'plan_type_id'    => 1
                ]);
            }

            $loan = $member->loans()->updateOrCreate([
                'cheque_number' => trim($row[$headers->search('Check Numbers')]),
            ], [
                'loan_date' => Carbon::parse(trim($row[$headers->search('Loan Date')])),
                'amount'    => Str::of($row[$headers->search('Loan Amount')])->trim()->replace(['$', ','], ''),
                'comment'   => trim($row[$headers->search('Comments')]) .
                    (!empty($paymentStart = trim($row[$headers->search('Payment Start')]))
                        ? ".\nPayment Start: " . $paymentStart
                        : null)
            ]);

            $loan->guarantors()->sync(
                collect($row)
                    ->only(array_keys($headers->filter(fn($header) => Str::startsWith($header, 'Guarantor'))->all()))
                    ->filter()
                    ->toArray()
            );
        }
    }

    public static function importRotessaCustomers($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($row = fgetcsv($handle, 1000)) !== false) {
            if (!is_array($row)) continue;

            $member = Member::findOrFail(trim($row[$headers->search('Gemach ID')]));

            if (!$member->membership_since) {
                Log::warning("[IMPORT ROTESSA CUSTOMERS (CSV)] No membership for member #$member->id creating membership...");
                $member->update([
                    'membership_type' => Member::TYPE_MEMBERSHIP,
                    'plan_type_id'    => 1
                ]);
            }

            if ($member->membership_since > trim($row[$headers->search('created_at')])) {
                $member->updateQuietly(['membership_since' => Carbon::parse(trim($row[$headers->search('created_at')]))]);
            }

            $paymentMethod = $member->paymentMethods()->firstOrNew([
                'gateway'            => 'Rotessa',
                'gateway_identifier' => trim($row[$headers->search('ID')])
            ]);

            $paymentMethod->data = [
                'Identifier'    => trim($row[$headers->search('identifier')]),
                'Customer Type' => trim($row[$headers->search('customer_type')]),
                'Bank Name'     => trim($row[$headers->search('bank_name')]),
            ];

            $paymentMethod->save();
        }
    }

    public static function importRotessaSchedules($filename)
    {
        $handle = self::handle($filename);

        $headers = self::headers($handle, $filename);

        while (($row = fgetcsv($handle, 1000)) !== false) {
            if (!is_array($row)) continue;

            $member = Member::whereHas(
                'paymentMethods',
                fn($q) => $q
                    ->where('gateway', 'Rotessa')
                    ->where('gateway_identifier', trim($row[$headers->search('customer_id')]))
            )->firstOrFail();

            $member->subscriptions()->updateOrCreate([
                'gateway'            => 'Rotessa',
                'gateway_identifier' => trim($row[$headers->search('id')])
            ], [
                'type'           => trim($row[$headers->search('Type')]) == 'Loan Payment'
                    ? Subscription::TYPE_LOAN_PAYMENT
                    : Subscription::TYPE_MEMBERSHIP,
                'amount'         => trim($row[$headers->search('amount')]),
                'start_date'     => Carbon::parse(trim($row[$headers->search('process_date')]))->toDateString(),
                'installments'   => trim($row[$headers->search('installments')]) ?: null,
                'frequency'      => [
                    'Once'              => Subscription::FREQUENCY_ONCE,
                    'Monthly'           => Subscription::FREQUENCY_MONTHLY,
                    'Every Other Month' => Subscription::FREQUENCY_BIMONTHLY,
                    'Yearly'            => Subscription::FREQUENCY_YEARLY,
                ][trim($row[$headers->search('frequency')])],
                'membership_fee' => Str::after(trim($row[$headers->search('Membership Fees')]), '$') ?: 0,
                'processing_fee' => trim($row[$headers->search('CC Fees')]) ?: 0,
                'decline_fee'    => trim($row[$headers->search('Late Fees')]) ?: 0,
                'active'         => (boolean)trim($row[$headers->search('active')]),
                'comment'        => trim($row[$headers->search('comment')]),
                'data'           => ['next_process_date' => trim($row[$headers->search('next_process_date')])]
            ])->update([
                // update amount to exclude fees
                'amount' => DB::raw('amount - membership_fee - processing_fee - decline_fee')
            ]);
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

    private static function headers($handle, $filename)
    {
        $headers = collect(fgetcsv($handle, 1000))->filter();

        if ($headers->isEmpty()) {
            fclose($handle);
            throw new Exception("No header row in $filename file");
        }

        return $headers;
    }
}
