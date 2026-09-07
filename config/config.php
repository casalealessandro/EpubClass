<?php

    /*@ini_set( 'upload_max_filesize' , '128M' );
    @ini_set( 'max_file_uploads' , '128M' );
    @ini_set( 'post_max_size', '1800M');
    @ini_set( 'memory_limit', '512M' );

    */
    @ini_set('display_errors', 1);
    @ini_set('display_startup_errors', 1);
    @error_reporting(E_ALL);

    define('_PATH_', dirname(__DIR__) . DIRECTORY_SEPARATOR);

    $httpsEnabled =
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0])) === 'https'
        );

    $scheme = $httpsEnabled ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');

    /*
     * PATH_INFO style routes such as /index.php/epub/file can make
     * SCRIPT_NAME contain the route suffix on the PHP built-in server.
     * Keep only the application path before /index.php so asset URLs
     * continue to point to the web root (or to the installation subfolder).
     */
    $indexPosition = strpos($scriptName, '/index.php');

    if ($indexPosition !== false) {
        $basePath = substr($scriptName, 0, $indexPosition);
    } else {
        $basePath = str_replace('\\', '/', dirname($scriptName));
    }

    if ($basePath === '/' || $basePath === '.') {
        $basePath = '';
    }

    define(
        '_BASE_URL_',
        $scheme . '://' . $host . ($basePath !== '' ? rtrim($basePath, '/') . '/' : '/')
    );

    define('_EPUB_PATH', _PATH_.'epub/');
    define('_EPUB_URL', _BASE_URL_.'epub/');
    define('_EPUB_URL_2', _BASE_URL_.'epub');
    define('_CONTROLLER_DIR', _PATH_.'controllers/');
    define('_CONTROLLER_DEF', _PATH_.'index.php/index');
    define('_MODEL_DIR', _PATH_.'modells/');
    define('_EXTENSION', 'epub');
    define('FTP_SERVER' , '');
    define('FTP_USER'   , '');
    define('FTP_PASS'   , '');


    // ** MySQL settings ** //

    define('DB_NAME', '');

    /** MySQL database username */
    define('DB_USER', '');

    /** MySQL database password */
    define('DB_PASSWORD', '');

    /** MySQL hostname */
    define('DB_HOST', '');



    require_once(_PATH_.'core/db.php');
    require_once(_PATH_.'core/autoload.php');
    //require_once(_PATH_.'/core/ajax.php');

    function url(){

        return  _BASE_URL_ ;

    }
