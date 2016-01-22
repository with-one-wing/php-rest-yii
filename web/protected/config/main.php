<?php
return [
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Rest',
    'modules' => array(
    ),
    // autoloading model and component classes
    'import' => [
        'application.models.*',
        'application.components.*',
        'application.components.exceptions.*',
        'application.helpers.*',
    ],
    'components' => [
        'cache' => [
            'class'=>'CApcCache',
        ],
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, error',
                ),
            ),
        ),

        'urlManager' => [
            'urlFormat' => 'path',
            'rules' => [
                'vote/<id:\d+>/<title:.*?>'=>'post/view',
                'votes/<tag:.*?>'=>'post/index',
                // REST patterns
                ['api/', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'GET'],
                ['api/update', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'PUT'],
                // Other controllers
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ],
        ],
        'db' => [
            'connectionString' => 'mysql:host=localhost;dbname=rest',
            'class'=>'system.db.CDbConnection',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '1',
            'charset' => 'utf8',
        ],
    ],
];
