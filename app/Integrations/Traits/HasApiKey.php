<?php

declare(strict_types=1);

namespace App\Integrations\Traits;

use App\Models\ApiKey;

interface HasApiKey
{
    public function setApiKey(ApiKey $apiKey): void;
}
