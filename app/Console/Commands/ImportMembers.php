<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImportMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:members {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import members from csv file in ./storage/imports';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = $this->argument('filename');

        $filepath = storage_path("imports/$filename");

        if (!file_exists($filepath)) {
            $this->error('file ' . $filepath . ' does not exist');
            exit();
        }

        $handle = fopen($filepath, "r");

        if ($handle !== false) {

            $firstLine = fgetcsv($handle, 1000); // header line

            while (($data = fgetcsv($handle, 1000)) !== false) {
                if (is_array($data)) {

                    if (trim(strtolower($data[1])) === 'child') continue;

                    $member = Member::create([
                        'id'        => $data[0],
                        'first_name' => $data[2],
                        'wife_name' => $data[3],
                        'last_name'   => $data[4],
                        'shtibel' => $data[10],
                        'home_phone' => $data[11],
                        'mobile_phone' => $data[12],
                        'hebrew_name' => $data[14],
                        'email' => $data[18],
                    ]);

                    if (!Str::startsWith($data[1], 'non')) {
                        $member->membership()->create([
                            'plan_type_id' => 1, // TODO
                            'type' => Membership::TYPE_MEMBERSHIP, //TODO
                        ]);
                    }
                }
            }

            rewind($handle);

            $firstLine = fgetcsv($handle, 1000); // header line

            while (($data = fgetcsv($handle, 1000)) !== false) {
                if (is_array($data)) {

                    if (trim(strtolower($data[1])) !== 'child') continue;

                    $member = Member::find($data[0]);

                    if (!$member) {
                        $this->warn("Member with id $data[0] not found for dependent");
                        Log::error("Member with id $data[0] not found for dependent", $data);
                        continue;
                    }

                    $member->dependents()->create([
                        'first_name' => $data[2],
                        'last_name'   => $data[4],
                        'dob' => DateTime::createFromFormat('d/m/Y', $data[20])
                            ? DateTime::createFromFormat('d/m/Y', $data[20])->format('Y-m-d')
                            : null,
                        'hebrew_name' => $data[14]
                    ]);
                }
            }

            fclose($handle);
        }
    }
}
