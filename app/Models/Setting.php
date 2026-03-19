<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get all settings as a key => value array.
     */
    public static function getAllAsArray(): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        return static::all()->pluck('value', 'key')->all();
    }

    /**
     * Get a single setting value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set a single setting value (upsert).
     */
    public static function set(string $key, mixed $value): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
