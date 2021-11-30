<?php

namespace Azuriom\Plugin\Vote\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Permission;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\Site;

class VoteServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__.'/../../vendor/autoload.php';
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        Permission::registerPermissions([
            'vote.admin' => 'vote::admin.permission',
        ]);

        ActionLog::registerLogModels([
            Reward::class,
            Site::class,
        ], 'vote::admin.logs');
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array
     */
    protected function routeDescriptions()
    {
        return [
            'vote.home' => 'vote::messages.title',
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array
     */
    protected function adminNavigation()
    {
        return [
            'vote' => [
                'name' => 'vote::admin.nav.title',
                'type' => 'dropdown',
                'icon' => 'fas fa-thumbs-up',
                'route' => 'vote.admin.*',
                'permission' => 'vote.admin',
                'items' => [
                    'vote.admin.settings' => 'vote::admin.nav.settings',
                    'vote.admin.sites.index' => 'vote::admin.nav.sites',
                    'vote.admin.rewards.index' => 'vote::admin.nav.rewards',
                    'vote.admin.votes.index' => 'vote::admin.nav.votes',
                ],
            ],
        ];
    }
}
