<p align="center">
  <a href="https://roots.io/sage/">
    <img alt="Sage" src="https://cdn.roots.io/app/uploads/logo-sage.svg" height="100">
  </a>
</p>

<p align="center">
  <a href="LICENSE.md">
    <img alt="MIT License" src="https://img.shields.io/github/license/roots/sage?color=%23525ddc&style=flat-square" />
  </a>

  <a href="https://packagist.org/packages/roots/sage">
    <img alt="Packagist" src="https://img.shields.io/packagist/v/roots/sage.svg?style=flat-square" />
  </a>

  <a href="https://github.com/roots/sage/actions">
    <img alt="Build Status" src="https://img.shields.io/github/workflow/status/roots/sage/Main?style=flat-square" />
  </a>

  <a href="https://twitter.com/rootswp">
    <img alt="Follow Roots" src="https://img.shields.io/twitter/follow/rootswp.svg?style=flat-square&color=1da1f2" />
  </a>
</p>

<p align="center">
  <a href="https://roots.io/"><strong><code>Website</code></strong></a> &nbsp;&nbsp; <a href="https://docs.roots.io/sage/10.x/installation/"><strong><code>Documentation</code></strong></a> &nbsp;&nbsp; <a href="https://github.com/roots/sage/releases"><strong><code>Releases</code></strong></a> &nbsp;&nbsp; <a href="https://discourse.roots.io/"><strong><code>Support</code></strong></a>
</p>

## Features

- Harness the power of [Laravel](https://laravel.com) and its available packages thanks to [Acorn](https://github.com/roots/acorn).
- Clean, efficient theme templating utilizing [Laravel Blade](https://laravel.com/docs/master/blade).
- Lightning fast frontend development workflow powered by [Bud](https://bud.js.org/).

## Conventions

- You must use `.eslintrc` rules in the app development.

## Requirements

Make sure all dependencies have been installed before moving on:

- [Acorn](https://docs.roots.io/acorn/2.x/installation/) v2
- [WordPress](https://wordpress.org/) >= 5.9
- [PHP](https://secure.php.net/manual/en/install.php) >= 7.4.0 (with [`php-mbstring`](https://secure.php.net/manual/en/book.mbstring.php) enabled)
- [Composer](https://getcomposer.org/download/)
- [Node.js](http://nodejs.org/) >= 16
- [Yarn](https://yarnpkg.com/en/docs/install)

## Theme installation

Install Sage in your WordPress themes directory and fire the following commands in directory:

1. `composer install`
2. `yarn && yarn build`

## Theme structure

```sh
themes/your-theme-name/   # → Root of your Sage based theme
├── app/                  # → Theme PHP
│   ├── Core/             # → Theme core files
│   ├── Providers/        # → Service providers
│   ├── View/             # → View models
│   ├── filters.php       # → Theme filters
│   └── setup.php         # → Theme setup
├── composer.json         # → Autoloading for `app/` files
├── public/               # → Built theme assets (never edit)
├── functions.php         # → Theme bootloader
├── index.php             # → Theme template wrapper
├── node_modules/         # → Node.js packages (never edit)
├── package.json          # → Node.js dependencies and scripts
├── resources/            # → Theme assets and templates
│   ├── fonts/            # → Theme fonts
│   ├── images/           # → Theme images
│   ├── scripts/          # → Theme javascript
│   ├── styles/           # → Theme stylesheets
│   └── views/            # → Theme templates
│       ├── components/   # → Component templates
│       ├── forms/        # → Form templates
│       ├── layouts/      # → Base templates
│       ├── partials/     # → Partial templates
        └── sections/     # → Section templates
├── screenshot.png        # → Theme screenshot for WP admin
├── style.css             # → Theme meta information
├── vendor/               # → Composer packages (never edit)
└── bud.config.js         # → Bud configuration
```

##  Theme Commands

- `yarn dev` — Compile assets when file changes are made, start Browsersync session
- `yarn build` — Compile assets for production

## Backend

### Types

Firestarter uses PHP types as much as possible...

### Modules

Theme uses [facade design pattern](https://refactoring.guru/design-patterns/facade/php/example) for managing internal dependencies, so instead of placing everything in the `setup.php` or `filters.php` files as `Sage` recommends, we should wrap custom features in specific boundaries placed in the `app` directory. Let's assume that we need to create `Posts` boundary that handles custom features for `Post` type. 

```sh
├── app/
│   ├── Posts/
│   ├── ├── Post.php
│   ├── ├── Posts.php
```

`App/Posts` is boundary context for `Posts` with `Posts.php` as fasade for internal actions. This facade should be initialized in the `App\App` like the here.

```php
namespace App;

use App\Core\Singleton;
use App\Posts\Posts;

class App extends Singleton
{
    private Posts $posts;

    protected function __construct()
    {
        $this->posts = fireclass(Posts::class); # <- Context Initialization
    }

    public function posts(): Posts
    {
        return $this->posts;
    }
}
```

This facade might be used everywhere you need using `firestarter` function. Example: `firestarter()->posts()->doSth()`.

### Hooks

Coditive theme a custom way for firing the WordPress hooks in the controllers. You can use `@action`, `@filter` and `@shortcode` in the comment block for initializing hooks in specific class.

```php
class Example {
  /**
   * @action template_redirect
   */
  public function sendMail(): void
  {
    wp_mail('test@example.com', 'Test Message', 'Test Content');
  }

  /**
   * @filter the_title
   */
  public function setTitle(string $title): string
  {
      return $title;
  }

  /**
   * @shortcode title
   */
  public function shortcode(): string
  {
      return get_the_title();
  }
}
```

But to make it work, there is a need to initialize instance using `fireclass` function: `fireclass(Example::class)`. Of course you can also put all the hoods in default way (constructor).

## Scripts

All the app modules should be initialized as `App` class attributes placed in `app.js` file.

### Routes

Routes module can be used for firing custom functions only on specific pages. 

#### Creation

Create route controller in `@scripts/routes` directory.

```js
class Home {
  init() {
  }

  finalize() {
  }
};
```

#### Initialization

Initialize route controller in `@scripts/modules/Router` constructor.

```js
this.routes = {
  home: new Home(),
};
```

#### Firing

Router will take all `<body>` classes and convert them to `camelCase` that become routes keys that will be used for firing specific events. So for example if you want to fire specific functions pages that have `template-home` class, create a new controller and assign it to `templateHome` in the router. The system will automatically fire `init` or `finalize` functions.

### Utils

`@scripts/utils` directory can be used for creating simple functions that may be used across the application. Please try to categorize them by meaing (for example, place array utils in `@scripts/utils/array.js` file).

## Blocks

Blocks can be used for building website content sections. The main advantage over `Sage` components is that assets are loaded on demand. So when specific block is not used on the page, its assets are not loaded at all. `Sage` components loads all the assets in the main `script.js` and `style.js` files, so assets are loaded even when not needed.

### Structure

```sh
├── app/
│   ├── View/
│   ├── ├── Blocks/
│   ├── ├── ├── Testimonial.php
├── resources/
│   ├── blocks/
│   ├── ├── testomonial/
│   ├── ├── ├── scripts.js
│   ├── ├── ├── styles.scss
│   ├── ├── ├── template.blade.php
```

### Creation

New block can be created using the following command. That's all. All the block controllers will be initialized automatically.

```sh
wp firestarter block create --name=Testimonial
```

### Using

```php
{!! firestarter()->block('testimonial')->render(); !!}
```

## Settings

Firestarter implements settings page...

## Assets

Firestarter contains two directories for assets:
- `resources/images` - directory for images that can be accessed in CSS files (for example `url('../../images/filename.jpg')`) and via the `@asset` directive (for example `@assets('images/filename.jpg')`)
- `resources/svg` - directory for images that can be accessed via `@svg` directive to get the full content of file, for example `@svg('svg/filename.svg')`

## Integrations

### ACF

#### Local JSON

Theme by default uses [Local JSON](https://www.advancedcustomfields.com/resources/local-json/) feature and stores fields sonfiguration in `/resources/fields` directory. You can disable this feature with the following `wp-config.php` entry.

```php
define('FIRESTARTER_INTEGRATIONS_ACF_LOCAL_JSON', false);
```

#### Blocks

Firestarter adds support for [ACF Blocks](https://www.advancedcustomfields.com/resources/blocks/). So you can use previously described blocks architecture with ACF without any additional work.

#### Settings

Firestarter implements support for [ACF Options](https://www.advancedcustomfields.com/resources/options-page/).

```php
firestarter()->settings()->get('site_logo')
```

TG: We should add here more information about this functionality, for me, it wasn't clear why I should use it instead of the "classic" get_field function.
