<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */

/**
 * Assertions.
 *
 * The Drupal project primarily uses runtime assertions to enforce the
 * expectations of the API by failing when incorrect calls are made by code
 * under development.
 *
 * @see http://php.net/assert
 * @see https://www.drupal.org/node/2492225
 *
 * If you are using PHP 7.0 it is strongly recommended that you set
 * zend.assertions=1 in the PHP.ini file (It cannot be changed from .htaccess
 * or runtime) on development machines and to 0 in production.
 *
 * @see https://wiki.php.net/rfc/expectations
 */
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

/**
 * Enable access to rebuild.php.
 *
 * This setting can be enabled to allow Drupal's php and database cached
 * storage to be cleared via the rebuild.php page. Access to this page can also
 * be gained by generating a query string from rebuild_token_calculator.sh and
 * using these parameters in a request to rebuild.php.
 */
$settings['rebuild_access'] = TRUE;

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

// Docksal DB connection settings.
$databases['default']['default'] = [
	'database' => 'default',
	'username' => getenv('MYSQL_USER'),
	'password' => getenv('MYSQL_PASSWORD'),
	'host' => 'db',
	'driver' => 'mysql',
];

// Workaround for permission issues with NFS shares.
$settings['file_chmod_directory'] = 0777;
$settings['file_chmod_file'] = 0666;

// File system settings.
$config['system.file']['path']['temporary'] = '/tmp';

// Reverse proxy configuration (Docksal vhost-proxy).
if (PHP_SAPI !== 'cli') {
	$settings['reverse_proxy'] = TRUE;
	$settings['reverse_proxy_addresses'] = array($_SERVER['REMOTE_ADDR']);
	// HTTPS behind reverse-proxy
	if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
	 $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' && !empty($settings['reverse_proxy']) &&
	  in_array($_SERVER['REMOTE_ADDR'], $settings['reverse_proxy_addresses'])) {
		$_SERVER['HTTPS'] = 'on';
		// This is hardcoded because there is no header specifying the original port.
		$_SERVER['SERVER_PORT'] = 443;
	}
}
