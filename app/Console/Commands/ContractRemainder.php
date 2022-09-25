<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ContractRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // $user = User::with('details.company.contracts')->whereRelation('details.company.contracts')->get()->toArray();
        // info($user);

        $words = [
            'aberration' => 'a state or condition markedly different from the norm',
            'convivial' => 'occupied with or fond of the pleasures of good company',
            'diaphanous' => 'so thin as to transmit light',
            'elegy' => 'a mournful poem; a lament for the dead',
            'ostensible' => 'appearing as such but not necessarily so'
        ];

        // Finding a random word
        $key = array_rand($words);
        $value = $words[$key];

        $contracts = Contract::with('company.users')->get();

        foreach ($contracts as $contract) {
            $diff = Carbon::now()->diffInDays(Carbon::parse($contract->end_date));
            if ($diff <= 15) {
                $users = $contract->company?->users;
                foreach ($users as $user) {
                    // info($user->email);
                    Mail::raw("{$key} -> {$value}", function ($mail) use ($user) {
                        $mail->from('info@viserx.com');
                        $mail->to($user->email)
                            ->subject('Contract Remainder');
                    });
                }
            } else {
                info('not in 15 days');
            }

            $this->info('This message sent to All Users');
        }

        // echo "this is my first command";
    }
}
