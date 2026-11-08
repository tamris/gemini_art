<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
        'type',
    ];

    /**
     * Get a setting value by key with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting || $setting->value === null) {
            return $default;
        }

        return match ($setting->type) {
            'integer', 'int' => (int) $setting->value,
            'float', 'decimal', 'numeric' => (float) $setting->value,
            'boolean', 'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set / update a setting value.
     */
    public static function set(string $key, mixed $value, ?string $label = null, string $group = 'general', string $type = 'string'): static
    {
        $stringValue = is_array($value) ? json_encode($value) : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'label' => $label ?? $key,
                'group' => $group,
                'type' => $type,
            ]
        );
    }
}
