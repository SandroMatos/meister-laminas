<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
                'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        
            'register' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/register',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/login[/:action]',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action' => 'index',
                    ],
                ],
            ],

        ],
    ],
    'controllers' => [
                'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        
            Controller\RegisterController::class => function($sm) {
                return new Controller\RegisterController(
                    $sm->get(Model\UsersTable::class),
                    $sm->get(Utils\Authentication::class),
                    $sm->get(Utils\Helper::class)
                );
            },
            Controller\LoginController::class => function($sm) {
                return new Controller\LoginController(
                    $sm->get(Utils\Authentication::class)
                );
            },

        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
'service_manager' =>  [
        'service_manager' => [
        'factories' => [
            
            'UsersTableGateway' => function ($sm) {
                $dbAdapter = $sm->get('Laminas\Db\Adapter\Adapter');
                $resultSetPrototype = new \Laminas\Db\ResultSet\ResultSet();
                //get base url from config
                $config = $sm->get('Config');
                $baseUrl = $config['view_manager']['base_url'];
    
                //pass base url via cnstructor to the User class
                $resultSetPrototype->setArrayObjectPrototype(new Model\Rowset\User($baseUrl));
                return new Utils\TableGateway('users', $dbAdapter, null, $resultSetPrototype);
            },
            'Application\Model\UsersTable' => function($sm) {
                $tableGateway = $sm->get('UsersTableGateway');
                $table = new Model\UsersTable($tableGateway);
    
                return $table;
            },
            Utils\Authentication::class => function($sm) {
                $auth = new Utils\Authentication(
                    $sm->get(\Laminas\Db\Adapter\Adapter::class),
                    $sm->get(Utils\Adapter::class)    
                );
                return $auth;
            },
            Utils\Helper::class => InvokableFactory::class,
            
            SessionManager::class => function ($container) {
                $config = $container->get('config');
                $session = $config['session'];
                $sessionConfig = new $session['config']['class']();
                $sessionConfig->setOptions($session['config']['options']);
                $sessionManager = new Session\SessionManager(
                    $sessionConfig,
                    new $session['storage'](),
                    null
                );
                \Laminas\Session\Container::setDefaultManager($sessionManager);
                
                return $sessionManager;
            },

        ],
    ],
    ],
];