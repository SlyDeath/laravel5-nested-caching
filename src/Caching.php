<?php

namespace SlyDeath\NestedCaching;

use Illuminate\Contracts\Cache\Repository as Cache;

class Caching
{
    /**
     * Поле инстанса кэша
     *
     * @var Cache
     */
    protected $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Кэширование
     *
     * @param $key
     * @param $fragment
     *
     * @return string
     */
    public function put($key, $fragment)
    {
        return $this->cache->tags(config('nested_caching.cache_tag'))
            ->rememberForever($this->applyKey($key), function () use ($fragment) {
                return $fragment;
            });
    }

    /**
     * Проверка на существование ключа в списке ключей
     *
     * @param $key
     *
     * @return boolean
     */
    public function has($key)
    {
        return $this->cache->tags(config('nested_caching.cache_tag'))
            ->has($this->applyKey($key));
    }

    /**
     * Нормализация ключа кэширования
     *
     * @param $key
     *
     * @return mixed
     */
    public function applyKey($key)
    {
        if (is_object($key) && method_exists($key, 'getNestedCacheKey')) {
            return $key->getNestedCacheKey();
        }

        return $key;
    }
}