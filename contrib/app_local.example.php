<?php

use function Cake\Core\env;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

define('DB_DEFAULTS', [
	'className' => Connection::class,
	'driver' => Mysql::class,
	'persistent' => false,
	'host' => env('MARIADB_HOST', 'mariadb'),
	'port' => env('MARIADB_PORT'),
	'username' => env('MARIADB_USER'),
	'password' => env('MARIADB_PASSWORD'),
	'encoding' => 'utf8mb4',
	'timezone' => 'UTC',
	'flags' => [],
	'cacheMetadata' => true,
	'log' => false,
	'quoteIdentifiers' => false,
]);

define('DB_NAME', [
	'default' => [
		'name' => 'app'
	]
]);

$datasources = [];
foreach (DB_NAME as $config => $db) {
	$datasources[$config] = DB_DEFAULTS;
	$datasources[$config]['database'] = isset($db['name']) ? $db['name'] : $config;
}
define('DATASOURCES_CONFIG', $datasources);
unset($datasources);

/**
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SECURITY_SALT__'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => DATASOURCES_CONFIG,

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
	'EmailTransport' => [
		'default' => [
			'className' => 'Smtp',
			'host' => env('MAIL_HOST', 'mailpit'),
			'port' => (int)env('MAIL_PORT', 1025),
			'username' => env('MAIL_USERNAME', null),
			'password' => env('MAIL_PASSWORD', null),
			'client' => null,
			'tls' => filter_var(env('MAIL_TLS', false), FILTER_VALIDATE_BOOL),
			'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
		],
	],
	'Email' => [
		'default' => [
			'transport' => 'default',
			'from' => [
				env('MAIL_FROM_ADDRESS', 'sans@example.com') => env('MAIL_FROM_NAME', 'Sans'),
			]
		]
	]
];
