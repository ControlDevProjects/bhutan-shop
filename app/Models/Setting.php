<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model {
    protected $primaryKey = 'key';
    public    $incrementing = false;
    protected $keyType      = 'string';
    protected $fillable     = ['key','value'];

    // Get a single setting value with optional default
    public static function get(string $key, mixed $default = null): mixed {
        return Cache::rememberForever('setting_'.$key, function() use ($key, $default) {
            $row = static::find($key);
            return $row ? $row->value : $default;
        });
    }

    // Set a single setting and bust cache
    public static function set(string $key, mixed $value): void {
        static::updateOrCreate(['key'=>$key], ['value'=>$value]);
        Cache::forget('setting_'.$key);
        Cache::forget('settings_all');
        Cache::forget('settings_arr');
    }

    // Get all settings as key=>value array (cached)
    /* public static function all($columns = ['*']): \Illuminate\Database\Eloquent\Collection {
        return Cache::rememberForever('settings_all', fn() => parent::all($columns));
    }

    public static function allAsArray(): array {
        return Cache::rememberForever('settings_arr', fn() =>
            parent::all()->pluck('value','key')->toArray()
        );
    } */

    public static function allAsArray(): array {
        return parent::all()->pluck('value','key')->toArray();
    }

    public static function bustCache(): void {
        Cache::flush(); // simple; fine for small apps
    }
}
