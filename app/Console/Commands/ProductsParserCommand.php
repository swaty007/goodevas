<?php

namespace App\Console\Commands;

use App\Facades\YsellApiFacade;
use App\Models\Ysell;
use Illuminate\Console\Command;

class ProductsParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product-parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $ysellKeys = Ysell::all();
        foreach($ysellKeys as $ysellKey) {
            /* @var $ysellKey Ysell */
            $api = YsellApiFacade::switchAuthKey($ysellKey->api_key);
        }
    }
}
