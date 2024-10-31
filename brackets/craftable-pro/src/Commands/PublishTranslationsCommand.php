<?php

namespace Brackets\CraftablePro\Commands;

use Brackets\CraftablePro\Translations\TranslationsProcessor;
use Illuminate\Console\Command;

class PublishTranslationsCommand extends Command
{
    /**
     * @var string
     */
    public $signature = 'craftable-pro:publish-translations';

    /**
     * @var string
     */
    public $description = 'Publish translations';

    public function __construct(private TranslationsProcessor $processor)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->processor->publishTranslations();
    }
}
