silex-autodocs
==============

Silex AutoDocs currently consists of one component, _automatic routing documentation_; more components are to follow.

Installation / Usage
--------------------

This can be installed via packagist by `require`ing `adamquaile/silex-autodocs` from packagist.

```
{
    "name": "Mercer App",
    "require": {
        "adamquaile/csvgenerator": "master",
        "adamquaile/silex-autodocs": "*",
        "twig/twig": ">=1.8,<2.0-dev",
        "symfony/twig-bridge": "2.1.*",
        "igorw/config-service-provider": "*",
        "symfony/security": "2.1.*",
        "silex/silex": "1.0.*",
        "doctrine/dbal": "2.2.*",
        "eventpad/flexibleregistrationform": "*",
        "monolog/monolog": ">=1.0.0"

    }
}
```

In your bootstrap file you must register the app in order to have the url `/autodocs/routes` available and usable.

```
<?php

$app = new \Silex\Application();
\AdamQuaile\Silex\AutoDocs\Routing::register($app);
```

All should be ready, now go to `/autodocs/routes`
