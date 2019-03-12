<?php

namespace SlyDeath\NestedCaching;

/**
 * Class BladeDirectives
 *
 * @package SlyDeath\NestedCaching
 */
class BladeDirectives
{
    /**
     * Список ключей кэша
     *
     * @var array $keys
     */
    protected $keys = [];
    
    /**
     * Список минут
     *
     * @var array $minutes
     */
    protected $minutes = [];
    
    /**
     * Инстанс кэша
     *
     * @var Caching $cache
     */
    protected $cache;
    
    /**
     * @param Caching $caching
     */
    public function __construct(Caching $caching)
    {
        $this->cache = $caching;
    }
    
    /**
     * Директива @cache
     *
     * @param string|object $key     Ключ кэширования
     * @param string|null   $minutes Время жизни кэша
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function cache($key, $minutes = null)
    {
        ob_start();
        
        return $this->cache->has($this->applyData($key, $minutes));
    }
    
    /**
     * Директива @endcache
     *
     * @return string
     */
    public function endCache()
    {
        return $this->cache->put($this->getKey(), ob_get_clean(), $this->getMinutes());
    }
    
    /**
     * Обработка ключа кэша
     *
     * @param string|object $key     Ключ кэширования
     * @param string|null   $minutes Время жизни кэша
     *
     * @return string
     *
     * @throws \Exception
     */
    public function applyData($key, $minutes = null)
    {
        switch (true) {
            // Обработка ключа указанного вручную
            case(is_string($key)):
                $key = trim($key);
                break;
            
            // Пытаемся получить ключ модели методом getNestedCacheKey
            case(is_object($key) && method_exists($key, 'getNestedCacheKey')):
                $key = $key->getNestedCacheKey();
                break;
            
            // Если это колекция, то для ключа кэша используем хэш её содержимого
            case($key instanceof \Illuminate\Support\Collection):
                $key = sha1($key);
                break;
            
            default:
                throw new NotDetermineKeyException('Could not determine an appropriate cache key');
        }
        
        $this->setKey($key);
        $this->setMinutes($minutes);
        
        return $key;
    }
    
    /**
     * Добаление ключа кэша в список ключей
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->keys[] = $key;
    }
    
    /**
     * Добаление минут в список минут
     *
     * @param null|int $minutes
     */
    public function setMinutes($minutes)
    {
        $this->minutes[] = now()->addMinutes($minutes);
    }
    
    /**
     * Получение ключа кэша из списка ключей
     *
     * @return string
     */
    public function getKey()
    {
        return array_pop($this->keys);
    }
    
    /**
     * Получение минут из списка минут
     *
     * @return string
     */
    public function getMinutes()
    {
        return array_pop($this->minutes);
    }
}
