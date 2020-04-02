<?php

namespace Azuriom\Plugin\Vote\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;

class VoteServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // \Azuriom\Plugins\Example\Middleware\ExampleMiddleware::class,
    ];

    /**
     * The plugin's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        // 'example' => \Azuriom\Plugins\Example\Middleware\ExampleRouteMiddleware::class,
    ];

    /**
     * The policy mappings for this plugin.
     *
     * @var array
     */
    protected $policies = [
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMiddlewares();
        //
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->registerPolicies();

        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();
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
                'items' => [
                    'vote.admin.settings' => 'vote::admin.nav.settings',
                    'vote.admin.sites.index' => 'vote::admin.nav.sites',
                    'vote.admin.rewards.index' => 'vote::admin.nav.rewards',
                ],
            ],
        ];
    }
}
