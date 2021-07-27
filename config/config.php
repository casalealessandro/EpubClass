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
	define('_BASE_URL_', 'http://192.168.0.11/edisesepub/');
	define('_EPUB_PATH', _PATH_.'epub/');
	define('_EPUB_URL', _BASE_URL_.'epub/');
	define('_EPUB_URL_2', 'http://localhost/edisesepub/epub');
	define('_CONTROLLER_DIR', _PATH_.'controllers/');
	define('_CONTROLLER_DEF', _PATH_.'index.php/index');
	define('_MODEL_DIR', _PATH_.'modells/');
	define('_EXTENSION', 'epub');
	define('FTP_SERVER' , 'ep2.qserver.it');
	define('FTP_USER'   , 'edisesprj');
	define('FTP_PASS'   , 'MjBlZGlzZXNwcmoxNQ1_');
	
	
	// ** MySQL settings ** //
	
	define('DB_NAME', 'edisesepub');

	/** MySQL database username */
	define('DB_USER', 'root');

	/** MySQL database password */
	define('DB_PASSWORD', 'root');

	/** MySQL hostname */
	define('DB_HOST', 'localhost');
	
	
	
	require_once(_PATH_.'core/db.php');
	require_once(_PATH_.'core/autoload.php');
	//require_once(_PATH_.'/core/ajax.php');
	
	function url(){
		
		return  _BASE_URL_ ;
	  
	}
	
	
	function getCovertedPrice($price = '12.99'){


		$price_converted = '';
		$your_api_key = '0330ea1627689a7286f0';
		
		$currency = array(	
							'US'=>'USD',
							'GB'=>'GBP',
							'CH'=>'CHF'
						);
		
		foreach($currency as $key => $cur){
			
			$string =  "EUR_" . $cur;
			//echo "https://free.currconv.com/api/v7/convert?q=" . $string . "&compact=ultra&apiKey=". $your_api_key;
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://free.currconv.com/api/v7/convert?q=" . $string . "&compact=ultra&apiKey=". $your_api_key,
				CURLOPT_RETURNTRANSFER => 1
			));
			
			$response = curl_exec($curl);
			$result = json_decode($response, true); 
			$rate = $result[$string];
			//echo $rate .'*'. $price.'<br />';
			
			$price_converted  = $rate * $price;
			
			
			$price_res[$cur] = number_format($price_converted,'2','.','.');
			
		}
	
		
		return $price_res;
	}
	
	
	
	