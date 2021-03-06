### WARNING!!!
This package no longer maintained, please, use https://github.com/SlyDeath/laravel-nested-caching instead

# Nested Caching для Laravel 5
[![Latest Stable Version](https://poser.pugx.org/slydeath/laravel5-nested-caching/v/stable)](https://packagist.org/packages/slydeath/laravel5-nested-caching)
[![Total Downloads](https://poser.pugx.org/slydeath/laravel5-nested-caching/downloads)](https://packagist.org/packages/slydeath/laravel5-nested-caching)
[![License](https://poser.pugx.org/slydeath/laravel5-nested-caching/license)](https://packagist.org/packages/slydeath/laravel5-nested-caching)

## Версии
Версия 3.* для Laravel 5.8, для 5.6-5.7 использовать релиз 2.*

## Установка

Добавить пакет в composer.json:

```bash
composer require slydeath/laravel5-nested-caching
```

Открыть `config/app.php` и добавить сервис провайдера в массив `providers`:

```php
SlyDeath\NestedCaching\NestedCachingServiceProvider::class,
```

Для размещения файла конфигурации выполнить:

```bash
php artisan vendor:publish --provider="SlyDeath\NestedCaching\NestedCachingServiceProvider" --tag=config
```

Для работы необходимы кэш-драйверы поддерживающие тэгирование - это **Memcached** или **Redis**.

В `.env` файле для **Memcached** указываем:

```
CACHE_DRIVER=memcached
```

для **Redis**:

```
CACHE_DRIVER=redis
```

Так же для работы **Redis** необходимо установить пакет `predis/predis`:

```bash
composer require predis/predis
```

## Как использовать?

### Кэширование любого отрезка html

Чтобы закэшировать любой произвольный кусок HTML, нужн опросто передать в директиву `@cache` ключ для кэширования фрагмента:

```html
@cache('simple-cache')
    <div>Это произвольный кусок HTML который будет закэширован по ключу "simple-cache"</div>
@endcache
```

### Кэширование моделей

Добавить в класс модели, которая будет кэшироваться, трейт `NestedCacheable`:

```php
use SlyDeath\NestedCaching\NestedCacheable;

class User extends Model
{
    use NestedCacheable;
}
```

В шаблоне, для кэширования модели, необходимо передать в директиву `@cache` её инстанс:

```html
@cache($user)
    <div>Кэширование модели App\User:</div>
    <ul>
        <li>Имя: {{ $user->name }}</li>
        <li>Email: {{ $user->email }}</li>
    </ul>
@endcache
```

### Кэширование модели на указанное время

Для кэширования модели на определённое время, указываем вторым параметром время жизни в минутах:

 ```html
 @cache($user, 1440)
     <div>...</div>
 @endcache
 ```

#### Обновление "родителя"

Чтобы при обновлении модели так же сбрасывался кэш "модели-родителя", 
необходимо обновлять поле `updated_at` у модели-родителя:

```php
use SlyDeath\NestedCaching\NestedCacheable;

class CarUser extends Model
{
    use NestedCacheable;

    protected $touches = ['user']; // Указываем родителя

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Пример использования:

**resources/views/user.blade.php**

```html
@cache($user)
    <section>
        <h2>Автомобили пользователя {{ $user->name }}</h2>
        <ul>
            @foreach($user->cars as $car)
                @include('user-car');
            @endforeach
        </ul>
    </section>
@endcache
```

**resources/views/user-car.blade.php**

```html
@cache($car)
    <li>{{ $car->brand }}</li>
@endcache
```

### Кэширование коллекций

Пример кэширования коллекции:

```html
@cache($users)

    @foreach ($users as $user)
        @include('user');
    @endforeach
    
@endcache
```
