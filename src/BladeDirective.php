<?php

namespace SlyDeath\NestedCaching;

class BladeDirectives
{
    /**
     * Список ключей кэша
     *
     * @var array $keys
     */
    protected $keys = [];

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
     * @param             $model
     * @param string|null $key
     *
     * @return bool
     */
    public function cache($model, $key = null)
    {
        ob_start();

        return $this->cache->has(
            $this->applyKey($model, $key)
        );
    }

    /**
     * Директива @endcache
     *
     * @return string
     */
    public function endCache()
    {
        return $this->cache->put(
            $this->getKey(), ob_get_clean()
        );
    }

    /**
     * Обработка ключа кэша
     *
     * @param  object $model
     *
     * @param string|null $key
     *
     * @return string
     * @throws \Exception
     */
    public function applyKey($model, $key = null)
    {
        switch (true) {
            // Обработка ключа указанного вручную
            case(is_string($model) || is_string($key)):
                $key = is_string($model) ? $model : $key;
                break;
            // Пытаемся получить ключ модели методом getNestedCacheKey
            case(is_object($model) && method_exists($model, 'getNestedCacheKey')):
                $key = $model->getNestedCacheKey();
                break;
            // Если это колекция, то для ключа кэша используем хэш её содержимого
            case($model instanceof \Illuminate\Support\Collection):
                $key = md5($model);
                break;
            default:
                throw new NotDetermineKeyException('Could not determine an appropriate cache key');
        }

        $this->setKey($key);

        return $key;
    }

    /**
     * Добаление ключа кэша в список ключей
     *
     * @param $key
     */
    public function setKey($key)
    {
        $this->keys[] = $key;
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
}