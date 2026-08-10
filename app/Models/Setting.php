<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'settings';

    /**
     * Les attributs modifiables en masse.
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
     * Cache duration in seconds.
     */
    protected static int $cacheDuration = 86400; // 24 heures

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES (HELPERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère la valeur d'un paramètre par sa clé.
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
     */
    public static function allSettings(): array
    {
        return Cache::remember('settings.all', self::$cacheDuration, function () {
            return self::orderBy('group')->orderBy('ordre')->get()->toArray();
        });
    }

    /**
     * Vide le cache des paramètres.
     */
    public static function clearCache(): void
    {
        Cache::forget('settings.all');
        
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
     * Retourne l'URL complète si c'est une image.
     */
    public function urlValue(): ?string
    {
        if ($this->type === 'image' && $this->value) {
            return asset('storage/' . $this->value);
        }
        return $this->value;
    }

    /**
     * Boot : vider le cache après chaque modification.
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