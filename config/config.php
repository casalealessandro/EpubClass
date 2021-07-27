<?php 

	/*@ini_set( 'upload_max_filesize' , '128M' );
	@ini_set( 'max_file_uploads' , '128M' );
	@ini_set( 'post_max_size', '1800M');
	@ini_set( 'memory_limit', '512M' );

	*/
	@ini_set('display_errors', 1);
	@ini_set('display_startup_errors', 1);
	@error_reporting(E_ALL);

	define('_PATH_', $_SERVER['DOCUMENT_ROOT'].'/edisesepub/');
	define('_BASE_URL_', 'http://localhost/edisesepub/');
	define('_EPUB_PATH', _PATH_.'epub/');
	define('_EPUB_URL', _BASE_URL_.'epub/');
	define('_EPUB_URL_2', 'http://localhost/edisesepub/epub');
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
	
	
	
	
	
	
	
