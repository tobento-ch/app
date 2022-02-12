# App

Base application for creating any PHP project.\
You might get started with the [**App Skeleton**](https://github.com/tobento-ch/app-skeleton) where you will find a list of available app bundles.

## Table of Contents

- [Getting Started](#getting-started)
	- [Requirements](#requirements)
- [Documentation](#documentation)
    - [App Lifecycle](#app-lifecycle)
    - [App Factory](#app-factory)
    - [App Resolving](#app-resolving)
        - [PSR-11](#psr-11)
        - [Autowiring](#autowiring)
        - [Definitions](#definitions)
        - [Make](#make)
        - [Call](#call)
        - [On](#on)  
    - [App Booting](#app-booting)
    - [App Directories](#app-directories)
    - [App Macros](#app-macros)
    - [Available Boots](#available-boots)
	   - [App Boot](#app-boot)
       - [Config Boot](#config-boot)
       - [Error Handling Boot](#error-handling-boot)
       - [Dater Boot](#dater-boot)
    - [Handle Boot Errors](#handle-boot-errors)
- [Credits](#credits)
___

# Getting Started

Add the latest version of the app project running this command.

```
composer require tobento/app
```

## Requirements

- PHP 8.0 or greater

# Documentation

## App Lifecycle

First, create the app from the provided app factory.\
Next, register any boot you want within your app.\
Finally, run your app.

```php
use Tobento\App\AppFactory;

// Create the app
$app = (new AppFactory())->createApp();

// Adding boots
$app->boot(\Tobento\App\Boot\App::class);
$app->boot(\Tobento\App\Boot\ErrorHandling::class);

// Run the app
$app->run();
```

## App Factory

```php
use Tobento\App\AppFactory;
use Tobento\App\AppFactoryInterface;
use Tobento\App\AppInterface;
use Tobento\Service\Resolver\ResolverFactoryInterface;
use Tobento\Service\Booting\BooterInterface;
use Tobento\Service\Dir\DirsInterface;

$appFactory = new AppFactory();

var_dump($appFactory instanceof AppFactoryInterface);
// bool(true)

$app = $appFactory->createApp(
    resolverFactory: null, // null|ResolverFactoryInterface
    booter: null, // null|BooterInterface
    dirs: null, // null|DirsInterface
);

var_dump($app instanceof AppInterface);
// bool(true)
```

**Parameters explanation**

| Parameter | Description |
| --- | --- |
| **resolverFactory** | If no resolver factory is set, it uses the [Resolver Container](https://github.com/tobento-ch/service-resolver-container). |
| **booter** | If no booter is set, it uses the [Default Booter](https://github.com/tobento-ch/service-booting#booter). |
| **dirs** | If no dirs is set, it uses the [Default Dirs](https://github.com/tobento-ch/service-dir#create-dirs). |

## App Resolving

### PSR-11

```php
use Tobento\App\AppFactory;

class Foo {}

$app = (new AppFactory())->createApp();

var_dump($app->has(Bar::class));
// bool(false)

var_dump($app->get(Foo::class));
// object(Foo)#2 (0) { }
```

### Autowiring

The app resolves any dependencies by autowiring, except build-in parameters needs a [definition](#definitions) to be resolved.

On union types parameter, the first resolvable parameter gets used if not set by definiton.

### Definitions

```php
use Tobento\App\AppFactory;
use Tobento\Service\Resolver\DefinitionInterface;

class Foo
{
    public function __construct(
        protected string $name
    ) {} 
}

$app = (new AppFactory())->createApp();

$definition = $app->set(Foo::class)->construct('name');

var_dump($definition instanceof DefinitionInterface);
// bool(true)
```

Check out the [**Resolver Definitions**](https://github.com/tobento-ch/service-resolver#definitions) to learn more about definitions in general.

### Make

```php
use Tobento\App\AppFactory;

class Foo
{
    public function __construct(
        private Bar $bar,
        private string $name
    ) {} 
}

class Bar {}

$app = (new AppFactory())->createApp();

$foo = $app->make(Foo::class, ['name' => 'value']);
```

Check out [**Resolver Make**](https://github.com/tobento-ch/service-resolver#make) to learn more about it.

### Call

```php
use Tobento\App\AppFactory;

class Foo
{
    public function index(Bar $bar, string $name): string
    {
        return $name;
    } 
}

class Bar {}

$app = (new AppFactory())->createApp();

$name = $app->call([Foo::class, 'index'], ['name' => 'value']);

var_dump($name);
// string(5) "value"
```

Check out [**Resolver Call**](https://github.com/tobento-ch/service-resolver#call) to learn more about it.

### On

```php
use Tobento\App\AppFactory;

class AdminUser {}
class GuestUser {}

$app = (new AppFactory())->createApp();

$app->on(AdminUser::class, GuestUser::class);

$user = $app->get(AdminUser::class);

var_dump($user);
// object(GuestUser)#16 (0) { }
```

Check out [**Resolver On**](https://github.com/tobento-ch/service-resolver#on) to learn more about it.

## App Booting

You may call the **booting** method to boot the registered boots so as having access to its functionality, otherwise it gets called on the **run** method.

```php
use Tobento\App\AppFactory;
use Tobento\App\Boot;

interface ServiceInterface {}
class Service implements ServiceInterface {}

class ServiceBoot extends Boot
{
    public function boot()
    {
        $this->app->set(ServiceInterface::class, Service::class);
    }
}

$app = (new AppFactory())->createApp();

$app->boot(ServiceBoot::class);

var_dump($app->has(ServiceInterface::class));
// bool(false)

// Do the booting.
$app->booting();

var_dump($app->has(ServiceInterface::class));
// bool(true)

// Run the app
$app->run();
```

## App Directories

You may add directories for its later usage.

```php
use Tobento\App\AppFactory;
use Tobento\Service\Dir\DirsInterface;

$app = (new AppFactory())->createApp();

$app->dirs()
    ->dir(dir: 'path/to/config', name: 'config', group: 'config')
    ->dir(dir: 'path/to/view', name: 'view');
    
var_dump($app->dir(name: 'view'));
// string(13) "path/to/view/"

var_dump($app->dirs() instanceof DirsInterface);
// bool(true)
```

Check out the [**Dir Service**](https://github.com/tobento-ch/service-dir) to learn more about dirs in general.

## App Macros

```php
use Tobento\App\AppFactory;

$app = (new AppFactory())->createApp();

$app->addMacro('lowercase', function(string $string): string {
    return strtolower($string);
});

var_dump($app->lowercase('Lorem'));
// string(5) "lorem"
```

Check out the [**Macro Service**](https://github.com/tobento-ch/service-macro) to learn more about macros in general.

## Available Boots

### App Boot

The app boot does the following:

* loads app config file if exist
* sets app environment based on app config
* adds specific config directory for environment
* sets timezone based on app config
* helper functions
* boots the specified boots from app config

```php
use Tobento\App\AppFactory;

$app = (new AppFactory())->createApp();

$app->boot(\Tobento\App\Boot\App::class);

$app->run();
```

### Config Boot

The config boot does the following:

* implements the config interface
* adds config macro

Check out the [**Config Service**](https://github.com/tobento-ch/service-config/#documentation) to learn more about it in general.

```php
use Tobento\App\AppFactory;
use Tobento\Service\Config\ConfigInterface;

$app = (new AppFactory())->createApp();

$app->dirs()->dir(
    dir: 'path/to/config',
    name: 'config',
    group: 'config'
);
    
$app->boot(\Tobento\App\Boot\Config::class);

$app->booting();

// using interface
$value = $app->get(ConfigInterface::class)->get(
    key: 'app.key',
    default: 'default',
    locale: 'de'
);

// using macro:
$value = $app->config('app.key', 'default');

var_dump($value);
// string(7) "default"

$app->run();
```

### Error Handling Boot

The error handling boot does the following:

* implements the error handling

```php
use Tobento\App\AppFactory;

$app = (new AppFactory())->createApp();

$app->boot(\Tobento\App\Boot\ErrorHandling::class);

$app->run();
```

### Dater Boot

The dater boot does the following:

* configures DateFormatter with the app.timezone and app.locale config

Check out the [**Dater Service**](https://github.com/tobento-ch/service-dater#documentation) to learn more about in general.

```php
use Tobento\App\AppFactory;
use Tobento\Service\Dater\DateFormatter;

$app = (new AppFactory())->createApp();

$app->boot(\Tobento\App\Boot\Dater::class);

$app->booting();

$df = $app->get(DateFormatter::class);

var_dump($df->date('now'));
// string(25) "Freitag, 11. Februar 2022"

$app->run();
```

## Handle Boot Errors

You might want to handle errors caused by boots in order to continue running the app:

```php
use Tobento\App\AppFactory;
use Tobento\App\Boot;
use Tobento\App\BootErrorHandlersInterface;
use Tobento\App\BootErrorHandlers;
use Tobento\Service\ErrorHandler\AutowiringThrowableHandlerFactory;

class CausesErrorBoot extends Boot
{
    public function boot(): void
    {
        echo $test();
    }
}

$app = (new AppFactory())->createApp();

$app->set(BootErrorHandlersInterface::class, function() use ($app) {

    $handlers = new BootErrorHandlers(
        new AutowiringThrowableHandlerFactory($app->container())
    );

    $handlers->add(function(Throwable $t): mixed {
        return null;
    });

    return $handlers;
});

$app->boot(\Tobento\App\Boot\ErrorHandling::class);

$app->boot(CausesErrorBoot::class);

$app->run();
```

Check out the [**Throwable Handlers**](https://github.com/tobento-ch/service-error-handler#throwable-handlers) to learn more about handlers in general.

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)