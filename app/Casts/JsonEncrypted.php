<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

class JsonEncrypted implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }
        try {
            $value = Crypt::decrypt(json_decode($value));
        } catch (DecryptException $e) {

        }

        return collect(json_decode($value));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }
        if ($value instanceof Collection) {
            $value = $value->toJson();
        } else {
            $value = json_encode($value);

            if ($value === false) {
                throw JsonEncodingException::forAttribute($this, $key, json_last_error_msg());
            }
        }

        return json_encode(Crypt::encrypt($value));
    }
}
