<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'ordre',
    ];

    /**
     * Durée du cache en secondes (24 heures).
     *
     * @var int
     */
    protected static int $cacheDuration = 86400;

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES (HELPERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère la valeur d'un paramètre par sa clé.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('setting.' . $key, self::$cacheDuration, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Définit la valeur d'un paramètre.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('setting.' . $key);
    }

    /**
     * Récupère tous les paramètres d'un groupe.
     *
     * @param string $group
     * @return array<string, mixed>
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember('settings.group.' . $group, self::$cacheDuration, function () use ($group) {
            return self::where('group', $group)
                ->orderBy('ordre')
                ->get()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Récupère tous les paramètres sous forme de tableau clé-valeur.
     *
     * @return array
     */
    public static function allSettings(): array
    {
        return Cache::remember('settings.all', self::$cacheDuration, function () {
            return self::orderBy('group')
                ->orderBy('ordre')
                ->get()
                ->toArray();
        });
    }

    /**
     * Récupère toutes les valeurs sous forme de tableau clé => valeur.
     *
     * @return array<string, mixed>
     */
    public static function allValues(): array
    {
        return Cache::remember('settings.values', self::$cacheDuration, function () {
            return self::query()->pluck('value', 'key')->all();
        });
    }

    /**
     * Vide le cache des paramètres.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget('settings.all');
        Cache::forget('settings.values');

        $groups = ['identite', 'contact', 'social', 'pages', 'seo', 'maintenance'];
        foreach ($groups as $group) {
            Cache::forget('settings.group.' . $group);
        }

        self::pluck('key')->each(function ($key) {
            Cache::forget('setting.' . $key);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES D'INSTANCE
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne l'URL complète si le paramètre est une image.
     *
     * @return string|null
     */
    public function urlValue(): ?string
    {
        if ($this->type === 'image' && $this->value) {
            return function_exists('media_url') ? media_url($this->value) : asset('storage/' . $this->value);
        }
        return $this->value;
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT
    |--------------------------------------------------------------------------
    */

    /**
     * Boot : vider le cache après chaque modification.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}