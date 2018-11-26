<?php

namespace SlyDeath\NestedCaching;

/**
 * Trait NestedCacheable
 *
 * @package SlyDeath\NestedCaching
 */
trait NestedCacheable
{
    /**
     * Создание уникального ключа кэширования
     *
     * @return string
     */
    public function getNestedCacheKey()
    {
        // Если нет id модели или поля updated_at то генерируется ключ из содержимого модели
        if ( ! $this->id || ! $this->updated_at) {
            return md5($this);
        }
        
        // Иначе составляется ключ модели
        $class_name = get_class($this);
        
        return "{$class_name}:{$this->id}:{$this->updated_at->timestamp}";
    }
}