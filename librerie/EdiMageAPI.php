<?php
	
class EdiMageAPI{
	
	
	public $magentoAPIURL;
	public $apiuser;      
	public $password;     
	public $token;
	public $storeUrl;
	
	/***SSO EXTENSION KEY**/
	public $client_id;
	public $secret_key; 
	public $storeConfig;
	
	
	public function __construct($url = '',$username = '',$password = '' ){
		
		$this->magentoAPIURL = $url;
		$this->apiuser       = $username;
		$this->password      = $password;
		$this->getToken();
		$this->baseUrl 	     =  $this->base_url();
		
		$this->client_id   = '';
		$this->secret_key  = '';
		$this->storeConfig();
		
		//$this->startSession();
	}
	
	private function getResponse($request,$headerS,$typeRequest = 'array',$customrequest = 'GET',$firstaccess='' ){
		
				
		try{
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => $this->magentoAPIURL.$request,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $customrequest,
			CURLOPT_POSTFIELDS =>$firstaccess,
			CURLOPT_HTTPHEADER => $headerS
				
			));
			

			
			$response = curl_exec($curl);
			
			
				
			
			curl_close($curl);
			
			if($typeRequest =='array'){
			
				$resp = json_decode($response, true);
			
			}else{
				
				$resp  = $response;
			}
			
		
			
			return $resp;
		}
		catch(Exception $e) {
			
			echo $e->getMessage();
		}
		
	}
	
	
	public function getToken(){
		
		try {
	
			$mage_token = "";
			
			$header[] = "Content-Type: application/json";
			$stringRequest = "integration/admin/token";
			$access = "{\"username\":\"".$this->apiuser."\", \"password\":\"".$this->password."\"}";
			$typeRequest = 'array';		
					
			
			
				$customrequest = 'POST';
				
				$this->token = $this->getResponse($stringRequest,$header,$typeRequest,$customrequest,$access);			
				$_SESSION['mage_token'] = $this->token;
				
				
			
			
			return $this->token;
	
		}
		catch(Exception $var) {
						
			 echo $var->getMessage();
		}
		
	}
	
	public function storeConfig(){
		
		$header[] = "Authorization: Bearer ".$this->token;
		
		$requestString = 'store/storeGroups';
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';
		
		$this->storeConfig = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		return $this->storeConfig;
		
		
	}
	

	

	public function search_ebook_by_text($arrayField){
		
		$header[] = "Authorization: Bearer ".$this->token;
		
		
		$name=$arrayField['name']; 
		$isbn=$arrayField['isbn']; 
		
		$requestString = "products?";
		if($isbn != '' && $name !=''){
			
			
			$sec_concat = '&';
		}
		
		
		if($isbn != '' && $name != ''){
			$requestString .= "searchCriteria[filterGroups][1][filters][0][field]=name&";
			$requestString .= "searchCriteria[filterGroups][1][filters][0][value]=%25".$name."%25&";
			$requestString .= "searchCriteria[filterGroups][1][filters][0][condition_type]=like&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][field]=isbn&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][value]=%25".$isbn."%25&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][condition_type]=like";
			
		
		}elseif($isbn != '' & $name == ''){
			
			$requestString .= "searchCriteria[filterGroups][0][filters][0][field]=isbn&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][value]=%25".$isbn."%25&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][condition_type]=like";
			
		}elseif($isbn == '' & $name != ''){
			
			$requestString .= "searchCriteria[filterGroups][0][filters][0][field]=name&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][value]=%25".$name."%25&";
			$requestString .= "searchCriteria[filterGroups][0][filters][0][condition_type]=like";
			
			
		}	
		//echo $requestString;
		
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';


		
		$product = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		
		
		
		return $product;
		
		
	}


	public function getProductsByID($id_product){
		
		
		$header[] = "Authorization: Bearer ".$this->token;
		
		$requestString = "products?searchCriteria[filter_groups][0][filters][0][field]=entity_id&searchCriteria[filter_groups][0][filters][0][value]=".$id_product."";
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';


		
		$product = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		
		return $product;
				
	}
	
	public function getProductsBySKU($sku){
		
		//$curl = curl_init();
		$header[] = "Authorization: Bearer ".$this->token;
		
		$requestString = "products?searchCriteria[filter_groups][0][filters][0][field]=sku&searchCriteria[filter_groups][0][filters][0][value]=".$sku."";
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';

		
		
		$product = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		
		return $product;
				
	}
	
	public function getData($key,$attributes_code){
		
		foreach ($attributes_code as $k=>$attribute) {
			
			
			
			if($attribute['attribute_code']==$key){
				
				return $attribute['value'];
			}
			
			
		}	
		
		
	}
	
	
	/* Get Cms Blocks Collection from store. */
    public function getCmsPage() {
		
			
		$this->getToken();
		
				
		//$curl = curl_init();
		$header[] = "Authorization: Bearer ".$this->token;
		
		$requestString = "cmsBlock/search?searchCriteria[sortOrders][0][field]=identifier&searchCriteria[sortOrders][0][direction]=asc";
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';
		

		$cms = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		return $cms;
		
    }
	
	public function add_to_cart($sku){
		
		$header[] = "Authorization: Bearer ".$this->token;
		
		$requestString = "guest-carts";
		$typeRequest = 'array';
		$customrequest = 'POST';
		
		
		$quote_id = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		$dataPost = array(
			'cartItem'=>array(
				'sku' => $sku,
				'qty' => "1",
				'quote_id' => $quote_id
			)
		);
		
		$firstaccess = json_encode($dataPost);
		$jsonrequest = '';
		$addProductApiUrl = "guest-carts/".$quote_id."/items";
		
		$headers[] = "Authorization: Bearer  ".$this->token;
		$headers[] = "Content-Type: application/json";
		
		$resultQuote  = $this->getResponse($addProductApiUrl,$headers,$jsonrequest,$customrequest,$firstaccess);
		
		return $resultQuote;
		
		
		
		
		
		
		
	}
	
	public function get_user_by_email($email){
	
		
		$this->getToken();
		
		
	
		$header[] = "Authorization: Bearer ".$this->token;
		
		$customername = $email;    

		$requestString = 'customers/search?searchCriteria[filter_groups][0][filters][0][field]=email&searchCriteria[filter_groups][0][filters][0][value]=%25'.$customername.'%25&searchCriteria[filter_groups][0][filters][0][condition_type]=like';
		//echo $requestString;
		
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';
		

		$resp = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		
		return $resp;
		
		
		
		
	}
	
	public function getUserById($id){
		
		
	
		$this->getToken();
				
		$header[] = "Authorization: Bearer ".$this->token;
			
		
		$requestString = 'customers/'.$id;
			
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';
		
	
		$resp = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
			
		return $resp;
		
		
	}
	
	/******MODIFICA CLIENTE******/
	
	public function updateUser($id_utente,$data){
		
	
		$header[]  = "Authorization: Bearer ".$this->token;
		$header[]  = "Content-Type: application/json";
		

		$requestString = 'customers/'.$id_utente;
		
		
		$typeRequest = 'array';
		$customrequest = 'PUT';
		$firstaccess = '';
		
	
		$resp = $this->getResponse($requestString,$header,$typeRequest,$customrequest,json_encode($data));
			
		
		if($resp['id']>0){
			
			return true;	
		}
		
		
		
				
		
		return false;
		
	}
	
	
	
	
	function get_token_querystring($url) {
		$url_parts = parse_url($url);
		if(empty($url_parts['query'])) return "";

		parse_str($url_parts['query'], $result_array);
		return $result_array["token"];
	}

	function remove_querystring_var($url, $key) {
		$url_parts = parse_url($url);
		if(empty($url_parts['query'])) return $url;

		parse_str($url_parts['query'], $result_array);
		unset($result_array[$key]);
		$url_parts['query'] = http_build_query($result_array);
		$url = (isset($url_parts["scheme"])?$url_parts["scheme"]."://":"").
			(isset($url_parts["user"])?$url_parts["user"].":":"").
			(isset($url_parts["pass"])?$url_parts["pass"]."@":"").
			(isset($url_parts["host"])?$url_parts["host"]:"").
			(isset($url_parts["port"])?":".$url_parts["port"]:"").
			(isset($url_parts["path"])?$url_parts["path"]:"").
			(isset($url_parts["query"])?"?".$url_parts["query"]:"").
			(isset($url_parts["fragment"])?"#".$url_parts["fragment"]:"");
		$url = rtrim($url, '?');
		return $url;
	}
	
	
	
	private function base_url(){
		$callbackProtocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
		
		$url = $callbackProtocol.$_SERVER['HTTP_HOST'];
		
		return $url;
			
		
		
		
	}
	
	
	/****************ORDINI*******************/
	public function getOrdersUser($email){
		
		//
		
		
		//$curl = curl_init();
		$header[] = "Authorization: Bearer ".$this->token;
		
		
		//API URL to get all Magento 2 modules
		$requestString = 'orders?searchCriteria[filter_groups][0][filters][0][field]=customer_email&searchCriteria[filter_groups][0][filters][0][value]='.$email;
		//echo $requestString;
		
		$typeRequest = 'array';
		$customrequest = 'GET';
		$firstaccess = '';
		
	
		$resp = $this->getResponse($requestString,$header,$typeRequest,$customrequest);
		
		
		return $resp;
		
		
	}
	
	
	
	
}
