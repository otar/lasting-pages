<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasEncodedId
{
    private const CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected static function bootHasEncodedId(): void
    {
        static::created(function (Model $model): void {
            /** @var self $model */
            $key = $model->getKey();
            if ($key !== null && is_numeric($key)) {
                $encodedId = $model->encodeId((int) $key);
                // Use direct attribute assignment to bypass fillable
                $model->encoded_id = $encodedId;
                $model->saveQuietly();
            }
        });
    }

    public function encodeId(int $id): string
    {
        if ($id === 0) {
            return self::CHARSET[0];
        }

        $base = strlen(self::CHARSET);
        $result = '';

        while ($id > 0) {
            $result = self::CHARSET[$id % $base].$result;
            $id = intval($id / $base);
        }

        return $result;
    }

    public function decodeId(string $string): int
    {
        $base = strlen(self::CHARSET);
        $length = strlen($string);
        $result = 0;

        /** @var array<string, int> $charMap */
        $charMap = array_flip(str_split(self::CHARSET));

        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            if (! isset($charMap[$char])) {
                throw new InvalidArgumentException("Invalid character '$char' in string");
            }
            $result = $result * $base + $charMap[$char];
        }

        return $result;
    }

    public function getRouteKeyName(): string
    {
        return 'encoded_id';
    }
}
