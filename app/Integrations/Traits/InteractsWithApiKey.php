<?php

declare(strict_types=1);

namespace App\Integrations\Traits;

use App\Models\ApiKey;

trait InteractsWithApiKey
{
    protected ?ApiKey $apiKey;

    public function setApiKey(ApiKey $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}
