Overview:
=
**menu-bundle** is a symfony bundle to display a menu according to the configuration stored in menu.yaml. routes stored under path and known to the system are recognized and displayed as "active", if this page is currently active. labels of the menu items are translated if they are stored in the translations.

Installation
=

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
-

Open a command console, enter your project directory and execute:

```console
$ composer require byte-artist/menu-bundle
```

Applications that don't use Symfony Flex
-

### Step 1: Install the Bundle

Open a command console, enter your project directory and execute the
following command to install the latest stable version of this bundle:

```console
$ composer require byte-artist/menu-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    ByteArtist\MenuBundle\MenuBundle::class => ['all' => true],
];
```

Configuration
=
Example:
```yaml
menu:
    type: default
    brand_name: Brand Name
    use_orig_css: true
    use_orig_js: true
    pages:
        label_home:
            path: existing_route
        label_user:
            path: label_user_index
            pages:
                label_user_create:
                    path: route_user_create
                label_user_edit:
                    path: route_user_edit
        label_admin:
            path: #
            pages:
                label_admin_overview:
                    path: route_admin_index
                label_admin_edit:
                    path: route_admin_edit
        label_contact:
            path: route_content
        label_imprint: https://www.byte-artist.de/imprint
```

- type: possible types: div, list, bootstrap and default (default is list)
- brand_name: Brand name which is displayed in the bootstrap menu (bootstrap type only)
- use_orig_css: Flag to control whether the css code delivered with the bundle should be used, if false, you have to provide it yourself
- use_orig_js: Flag to control whether the javascript code delivered with the bundle should be used, if false, you have to provide it yourself
- pages: list of the structure belonging to the menu
  - route_name: name of the menu item, if it exists in the translations it will be translated, otherwise the item will just be displayed as specified here

    [
      - path: name to an existing route of an action, a normal url or just a '#' is also possible.
      - pages: optional, any subpages

    ]

Usage
=

To display the menu, it is sufficient to call the Twig function `menu` in a Twig template, wherever it is to be displayed: 

```
// layout.html.twig
{{ menu() }}
```

Troubleshooting
=
if for some reason the symfony repository for the contrib recipes is not available, add the following lines in your composer.json:

```
"extra": {
    "symfony": {
        "endpoint": [
            "https://api.github.com/repos/mastercad/symfony-recipes/contents/index.json",
            "flex://defaults"
        ]
    }
}
```
