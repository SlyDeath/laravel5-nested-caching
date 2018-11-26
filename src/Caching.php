<?php

namespace SlyDeath\NestedCaching;

use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Class Caching
 *
 * @package SlyDeath\NestedCaching
 */
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
     * @param string|object $key      Ключ кэширования
     * @param string        $fragment Вывод
     * @param string|null   $minutes  Время жизни кэша
     *
     * @return string
     */
    public function put($key, $fragment, $minutes = null)
    {
        if ($minutes) {
            return $this->cache->tags(config('nested_caching.cache_tag'))
                               ->remember($key, $minutes, function () use ($fragment) {
                                   return $fragment;
                               });
        }
        
        return $this->cache->tags(config('nested_caching.cache_tag'))
                           ->rememberForever($key, function () use ($fragment) {
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
        return $this->cache->tags(config('nested_caching.cache_tag'))->has($key);
    }
}