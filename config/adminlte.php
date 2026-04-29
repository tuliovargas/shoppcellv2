<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/6.-Basic-Configuration
    |
    */

    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/6.-Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/6.-Basic-Configuration
    |
    */

    'logo' => 'AdminLTE',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'AdminLTE',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/6.-Basic-Configuration
    |
    */

    'usermenu_enabled' => false,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/7.-Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/7.-Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/7.-Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/7.-Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => false,
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/7.-Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/6.-Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/9.-Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/8.-Menu-Configuration
    |
    */

    'menu' => [
        [
            'text' => 'PDV',
            'icon' => 'fas fa-credit-card mr-2',
            'url' => 'pdv',
            'can' => 'use pos',
            'topnav' => true
        ],
        [
            'text' => 'Caixa',
            'icon' => 'fas fa-cash-register mr-2',
            'url' => 'cashier',
            'can' => 'use cashier',
            'topnav' => true
        ],
        [
            'text' => 'Manutenção',
            'icon' => 'fas fa-briefcase mr-2',
            'url' => 'maintenances',
            'can' => 'use maintenance',
            'topnav' => true
        ],
        [
            'text' => 'Cadastros',
            'icon' => 'fas fa-address-card mr-2',
            'can' => 'register',
            'submenu' => [
                [
                    'text' => 'Produtos',
                    'url' => 'products',
                    'icon' => 'fas fa-cubes mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register product',
                ],
                [
                    'text' => 'Clientes',
                    'url' => 'clients',
                    'icon' => 'fas fa-user mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register client',
                ],
                [
                    'text' => 'Pedidos',
                    'url' => 'orders',
                    'icon' => 'fas fa-receipt mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register order',
                ],
                [
                    'text' => 'Estoque',
                    'url' => 'stocks',
                    'icon' => 'fas fa-arrow-alt-circle-up mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register order',
                ],
                [
                    'text' => 'Fornecedores',
                    'url' => 'suppliers/',
                    'icon' => 'fas fa-dolly mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register supplier',
                ],
                [
                    'text' => 'Categorias',
                    'url'  => 'categories',
                    'icon' => 'fas fa-archive mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register category',
                ],
                [
                    'text' => 'Marcas',
                    'url'  => '/brands',
                    'icon' => 'fas fa-bolt mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register category',
                ],
                [
                    'text' => 'Modelos',
                    'url' => 'brand-models',
                    'icon' => 'fab fa-buffer mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register model',
                ],
                [
                    'text' => 'Cupons',
                    'url' => 'coupons',
                    'icon' => 'fas fa-tag mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register order',
                ],
                [
                    'text' => 'Despesas',
                    'url' => 'expenses',
                    'icon' => 'fas fa-anchor mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register order',
                ],
                [
                    'text' => 'Promoções',
                    'url' => 'promotions',
                    'icon' => 'fas fa-inbox mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register promotions',
                ],
            ],
        ],
        [
            'text' => 'Relatórios',
            'icon' => 'fas fa-chart-bar mr-2',
            'can' => 'use report',
            'submenu' => [
                [
                    'text' => 'Vendas',
                    'url' => 'reports/sales',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Caixa',
                    'url' => 'reports/cashier',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Manutenção',
                    'url' => 'reports/maintenance',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Comissão',
                    'url' => 'reports/commission',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Pedidos',
                    'url' => 'reports/requests',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Estoque',
                    'url' => 'reports/inventory',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Despesas',
                    'url' => 'reports/expenses',
                    'icon' => 'fas fa-address-book mr-2',
                    'classes' => 'ml-3',
                ],
            ]
        ],
        [
            'text' => 'Configuração',
            'icon' => 'fas fa-cogs mr-2',
            'can' => 'update config',
            'submenu' => [
                [
                    'text' => 'Checklist',
                    'url' => 'configurations/checklists',
                    'icon' => 'fas fa-check-square mr-2',
                    'classes' => 'ml-3',
                ],
                [
                    'text' => 'Sistema',
                    'url' => 'configurations/system',
                    'icon' => 'fas fa-compress mr-2',
                    'classes' => 'ml-3',
                    'can' => 'update system config',
                ],
                [
                    'text' => 'Parcelamento',
                    'url' => 'configurations/tax-installments',
                    'icon' => 'fas fa-percent mr-2',
                    'classes' => 'ml-3',
                    'can' => 'update installment config',
                ],
                [
                    'text' => 'Métodos de Pagamento',
                    'url' => 'payment-methods',
                    'icon' => 'fas fa-credit-card',
                    'classes' => 'ml-3',
                    'can' => 'register payment method'
                ],
                [
                    'text' => 'Usuários',
                    'url'  => 'users',
                    'icon' => 'fas fa-user-tie mr-2',
                    'classes' => 'ml-3',
                    'can' => 'register user',
                ],
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/8.-Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/9.-Other-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'bsCustomFileInput' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js',
                ]
            ],
        ],
        'InputMask' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/gh/RobinHerbots/jquery.inputmask@5.0.5/dist/jquery.inputmask.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/9.-Other-Configuration
    */

    'livewire' => false,
];
