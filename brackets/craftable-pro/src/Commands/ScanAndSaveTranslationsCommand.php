<?php

namespace Brackets\CraftablePro\Commands;

use Brackets\CraftablePro\Translations\TranslationsProcessor;
use Illuminate\Console\Command;

class ScanAndSaveTranslationsCommand extends Command
{
    /**
     * @var string
     */
    public $signature = 'craftable-pro:scan-translations';

    /**
     * @var string
     */
    public $description = 'Scan translations';

    public function __construct(private TranslationsProcessor $processor)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->processor->scanTranslations();
    }
}
