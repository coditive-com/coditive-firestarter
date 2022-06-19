<?php

namespace App;

use App\Core\Singleton;
use App\Core\Settings;
use App\Core\Setup;
use App\Integrations\Integrations;
use App\View\Blocks\Blocks;
use App\View\Blocks\Block;

define('APP_NAME', 'Firestarter');
define('APP_SLUG', 'firestarter');
define('APP_VERSION', '1.0.0');
define('APP_PATH', dirname(__FILE__, 2));
define('APP_FILE', APP_PATH . '/functions.php');
define('APP_ASSETS_PATH', APP_PATH . '/public');
define('APP_RESOURCES_PATH', APP_PATH . '/resources');
define('APP_ASSETS_URI', get_stylesheet_directory_uri() . '/public');
define('APP_RESOURCES_URI', get_stylesheet_directory_uri() . '/resources');

class App extends Singleton
{
    private Blocks $blocks;

    private Integrations $integrations;

    private Settings $settings;

    private Setup $setup;

    protected function __construct()
    {
        $this->blocks = fsclass(Blocks::class);
        $this->integrations = fsclass(Integrations::class);
        $this->settings = fsclass(Settings::DEFAULT);
        $this->setup = fsclass(Setup::class);
    }

    public function blocks(): Blocks
    {
        return $this->blocks;
    }

    public function block(string $id): ?Block
    {
        return $this->blocks->get($id);
    }

    public function integrations(): Integrations
    {
        return $this->integrations;
    }

    public function settings(): Settings
    {
        return $this->settings;
    }

    public function setup(): Setup
    {
        return $this->setup;
    }
}
