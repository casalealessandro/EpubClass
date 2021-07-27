<?php 

class Edigita{
	
	$username;
	$password;
	
	public function __construct(){
		
		$this->usename = '';
		$this->password = '';
	}
	
	public function create_onix($post){
			
		
		
			
			
			$autori_desc = '';
			
			
					
					
			$isbn = $post['isbn'];
			
			
			$dom  = new DOMDocument('1.0', 'utf-8'); 
			
			$dom->formatOutput = true;
			$dom->preserveWhiteSpace = false;
			
			$ONIXMessage = $dom->createElement('ONIXMessage');
			$ONIXMessage->setAttribute('xmlns', 'http://www.editeur.org/onix/3.0/reference');
			$ONIXMessage->setAttribute('release', '3.0');
		
			$header = $dom->createElement('header' );
			$sender = $dom->createElement('sender','alessandro.casale@edises.it' );
			
			
			
			$header->appendChild($sender);
			$ONIXMessage->appendChild($header);
			
			$product = $dom->createElement('Product');
			
			/**** Codifica del record ****/
			
			$RecordReference = $dom->createElement('RecordReference','edises_'.$isbn);
			
			
			
			/****03 Prodotto completo, 04 Update, 05 Delete****/
			
			
			$NotificationType = $dom->createElement('NotificationType',$post['azione']);

			$ProductIdentifier = $dom->createElement('ProductIdentifier');
			$ProductIDType = $dom->createElement('ProductIDType', '03');
			$IDValue = $dom->createElement('IDValue', $isbn);

			$product->appendChild($RecordReference);
			$product->appendChild($NotificationType);
			$ProductIdentifier->appendChild($ProductIDType);
			$ProductIdentifier->appendChild($IDValue);
			
			$product->appendChild($ProductIdentifier);
			/***Inizio DescriptiveDetail**/
			
			$DescriptiveDetail = $dom->createElement('DescriptiveDetail');
			
			/***Inizio tipo file**/
			
			/***** Prodotto singolo *****/
			$ProductComposition = $dom->createElement('ProductComposition','00');
			$ProductForm = $dom->createElement('ProductForm','EA');
			
			/**** E101 Epub ****/
			
			$ProductFormDetail1 = $dom->createElement('ProductFormDetail','E101');
			$ProductFormDetail2 = $dom->createElement('ProductFormDetail','E200');
			
			/**** Testo ****/
			$PrimaryContentType = $dom->createElement('PrimaryContentType','10');

			/**** Protezione: 00 Nessuna, 02 Filigrana/Watermark, 03 Adobe DRM ****/
			$EpubTechnicalProtection = $dom->createElement('EpubTechnicalProtection','03');
			 
			/**** Fine tipo file ****/
			
			$DescriptiveDetail->appendChild($ProductComposition);
			$DescriptiveDetail->appendChild($ProductForm);
			$DescriptiveDetail->appendChild($ProductFormDetail1);
			$DescriptiveDetail->appendChild($ProductFormDetail2);
			$DescriptiveDetail->appendChild($PrimaryContentType);
			$DescriptiveDetail->appendChild($EpubTechnicalProtection);
			$product->appendChild($DescriptiveDetail);
			
			
			
			/**** Inizio collezioni/serie ****/
			
			
			
			$Collection 		= 	$dom->createElement('Collection');
			$CollectionType 	= 	$dom->createElement('CollectionType', '10');
			$TitleDetail    	= 	$dom->createElement('TitleDetail');
			$TitleType      	=	$dom->createElement('TitleType', '01');
			$TitleElement   	=	$dom->createElement('TitleElement');
			
			$TitleElementLevel  =	$dom->createElement('TitleElementLevel', '02');
			$TitleText   		=	$dom->createElement('TitleText', $_POST['collana']);
				
			/**** Fine collezioni/serie ****/
			
			
			$TitleElement->appendChild($TitleElementLevel);
			$TitleElement->appendChild($TitleText);
			$TitleDetail->appendChild($TitleType);
			$TitleDetail->appendChild($TitleElement);
			$Collection->appendChild($CollectionType);
			$Collection->appendChild($TitleDetail);
			
			$DescriptiveDetail->appendChild($Collection);
			
			/**** Inizio titolo pubblicazione ****/
			
			$TitleDetail 				= $dom->createElement('TitleDetail');
			$TitleType 					= $dom->createElement('TitleType','01');
			$TitleElement 				= $dom->createElement('TitleElement');
			$TitleElementLevel 			= $dom->createElement('TitleElementLevel','01');
			$TitleText 					= $dom->createElement('TitleText',$post['title']);
			$TitlePrefix 				= $dom->createElement('TitlePrefix','');
			$TitleWithoutPrefix 		= $dom->createElement('TitleWithoutPrefix','');
			$Subtitle 					= $dom->createElement('Subtitle',$post['sottotitolo']);
			 
			/**** Fine titolo pubblicazione ****/ 
			
			
			$TitleElement->appendChild($TitleElementLevel);
			$TitleElement->appendChild($TitleText);
			$TitleElement->appendChild($TitlePrefix);
			$TitleElement->appendChild($TitleWithoutPrefix);
			$TitleElement->appendChild($Subtitle);
			$TitleDetail->appendChild($TitleType);
			$TitleDetail->appendChild($TitleElement);
			$DescriptiveDetail->appendChild($TitleDetail);
			
			/**** Inizio contributori / Autori ****/ 
			
			
			
			$autori = $post['autori'];
			
			foreach($autori as $autore){
				if($autore['nome']  != '' && $autore['cognome'] !=''):
			
					$SequenceNumber 	= $dom->createElement('SequenceNumber','1');
					$ContributorRole 	= $dom->createElement('ContributorRole','A01');
					$NamesBeforeKey 	= $dom->createElement('NamesBeforeKey',$autore['nome']);
					$KeyNames 			= $dom->createElement('KeyNames',$autore['cognome']);
					$Website 			= $dom->createElement('Website');
					
					/**
					WebsiteRole : 
					01 TIPO SITO WEB 
					42 SOCIAL 
					
					**/
					
					$WebsiteRole 			= $dom->createElement('WebsiteRole','01');
					$WebsiteLink 			= $dom->createElement('WebsiteLink','');
					
					$Website->appendChild($WebsiteRole);
					$Website->appendChild($WebsiteLink);
					
					if(isset($autore['autori_desc']))
					{
						$autori_desc = $autore['autori_desc'];
					}
					
					$BiographicalNote = $dom->createElement('BiographicalNote', $autori_desc);
					$ContributorPlace = $dom->createElement('ContributorPlace');
					/**** 08 Cittadino di ****/
					$ContributorPlaceRelator 	= $dom->createElement('ContributorPlaceRelator', '08');
					
					/**** Codice Paese ****/
					$ContributorCountryCode 	= $dom->createElement('CountryCode', 'IT');
					
					$ContributorPlace->appendChild($ContributorPlaceRelator);
					$ContributorPlace->appendChild($ContributorCountryCode);
					
					/**** Fine contributori ****/
					
					$Contributor = $dom->createElement('Contributor');
							
					$Contributor->appendChild($SequenceNumber);
					$Contributor->appendChild($ContributorRole);
					$Contributor->appendChild($NamesBeforeKey);
					$Contributor->appendChild($KeyNames);
					$Contributor->appendChild($Website);
					$Contributor->appendChild($BiographicalNote);
					$Contributor->appendChild($ContributorPlace);
					$DescriptiveDetail->appendChild($Contributor);
					
				endif;
			}
			
			
			/**** 
				Numero di pagine della pubblicazione 
				00, 08 : Page count
				03 : Front matter page count (Roman-numbered) pages
				22 : File size 
				
			****/
			
			$Extent = $dom->createElement('Extent');
			$ExtentType = $dom->createElement('ExtentType', '00');
			$ExtentValue = $dom->createElement('ExtentValue', '256');
			/**** 03 : Pages, 19 : Megabytes (solo se scelgo 22 come ExtentType );***/ 
			$ExtentUnit = $dom->createElement('ExtentUnit','03');
			
			$Extent->appendChild($ExtentType);
			$Extent->appendChild($ExtentValue);
			$Extent->appendChild($ExtentUnit);
			
			/**** Fine n.pagine ****/
			$DescriptiveDetail->appendChild($Extent);
			
			
		
			/**** Lingua originale ****/
			
			$Language 		= $dom->createElement('Language');
			$LanguageRole 	= $dom->createElement('LanguageRole', '01');
			$LanguageCode 	= $dom->createElement('LanguageCode', 'ita');
			
			$Language->appendChild($LanguageRole);
			$Language->appendChild($LanguageCode);
			
			/**** Fine Lingua originale ****/
			$DescriptiveDetail->appendChild($Language);
			
			/**** Inizio Classifications(BISAC)****/
			$Subject = $dom->createElement('Subject');
			
			/***SELEZIONARE LA BISAC PRIMARIA****/
			$MainSubjectì = $dom->createElement('MainSubject');
			
			/****
			
				01 : Dewey
				10 : BISAC
				20 : Keywords
				24 : Custom
				28 : Electre
				29 : CLIL 
			
			****/
			
			
			$bisacPrimary = $post['bisac']['0'];
			
			$SubjectSchemeIdentifier = $dom->createElement('SubjectSchemeIdentifier','10');
			$SubjectCode			 = $dom->createElement('SubjectCode',$bisacPrimary);
			
			$Subject->appendChild($MainSubjectì);
			$Subject->appendChild($SubjectSchemeIdentifier);
			$Subject->appendChild($SubjectCode);
			$DescriptiveDetail->appendChild($Subject);
			
			
			
			foreach($post['bisac'] as $bisac){
			
				$Subject = $dom->createElement('Subject');
				$SubjectSchemeIdentifier = $dom->createElement('SubjectSchemeIdentifier','10');
				$SubjectCode			 = $dom->createElement('SubjectCode', $bisac);
				
				
				$Subject->appendChild($SubjectSchemeIdentifier);
				$Subject->appendChild($SubjectCode);
				
				$DescriptiveDetail->appendChild($Subject);
			}
			
			
			/*$Subject = $dom->createElement('Subject');
			$SubjectSchemeIdentifier = $dom->createElement('SubjectSchemeIdentifier','10');
			$SubjectCode			 = $dom->createElement('SubjectCode', $post['bisac']['2']);
			
			
			$Subject->appendChild($SubjectSchemeIdentifier);
			$Subject->appendChild($SubjectCode);*/
			
			/**** FIne Classifications(BISAC)****/
			//$DescriptiveDetail->appendChild($Subject);
			
			/**Inizio Parole chiavi*/
			
			$KeySubject = $dom->createElement('Subject');
			$KeySubjectSchemeIdentifier 	= $dom->createElement('SubjectSchemeIdentifier','20');
			$KeySubjectHeadingText 			= $dom->createElement('SubjectHeadingText',$post['keyword']);
			
			$KeySubject->appendChild($KeySubjectSchemeIdentifier);
			$KeySubject->appendChild($KeySubjectHeadingText);
			
			/**** FIne Parole chiavi****/
			
			
			$DescriptiveDetail->appendChild($KeySubject);
			/***Fine DescriptiveDetail***/
			
			/****Inizio Informazioni aggiuntive: indice, sommario...****/
			$CollateralDetail = $dom->createElement('CollateralDetail');
			
			
			$TextContent = $dom->createElement('TextContent');
			
			/****
				Text type.
				02 : Comments
				03 : Summary
				04 : Table of contents
				05 : Presentation
				06 : Review quote
				09 : Endorsement
				10 : Promotional headline
				12 : Biographical note
				14 : Excerpt
				17 : Serie Description (see Collection) 
			****/
			
			$TextType = $dom->createElement('TextType', '05');
		   
			$ContentAudience = $dom->createElement('ContentAudience','00');
		   
			$Text = $dom->createElement('Text',$post['description']);
			
			$TextContent->appendChild($TextType);
			$TextContent->appendChild($ContentAudience);
			$TextContent->appendChild($Text);
			$CollateralDetail->appendChild($TextContent);
			
			
			/*******Riassunto********/
			
			
			$TextContent = $dom->createElement('TextContent');
			
			
			
			$TextType = $dom->createElement('TextType', '03');
		   
			$ContentAudience = $dom->createElement('ContentAudience','00');
		   
			
			$Text = $dom->createElement('Text',$post['meta_description']);
			
			$TextContent->appendChild($TextType);
			$TextContent->appendChild($ContentAudience);
			$TextContent->appendChild($Text);
			$CollateralDetail->appendChild($TextContent);
			
			
			
			
			$product->appendChild($CollateralDetail);
			/****fine Informazioni aggiuntive****/
			
			
			
			/****Inizio dettagli pubblicazione...****/
			$PublishingDetail 	= $dom->createElement('PublishingDetail');
			$publisher 					= $dom->createElement('publisher');
			
			
			$publisherPublishingRole 	= $dom->createElement('PublishingRole','01');
			$publisher->appendChild($publisherPublishingRole);
			
			$publisherWebsite 			= $dom->createElement('Website');
			$publisherWebsiteRole		= $dom->createElement('WebsiteRole', '02');	
			$publisherWebsiteLink		= $dom->createElement('WebsiteLink', '');	 
			
			$publisherWebsite->appendChild($publisherWebsiteRole);
			$publisherWebsite->appendChild($publisherWebsiteLink);
			$publisher->appendChild($publisherWebsite);
			$publisher->appendChild($publisherWebsite);

			$PublishingDetail->appendChild($publisher);
			
			$PublishingStatus 		= $dom->createElement('PublishingStatus','04'); //04 : Active 08 : Inactive
			$PublishingDetail->appendChild($PublishingStatus);
			
			$PublishingDate         = $dom->createElement('PublishingDate');
			$PublishingDateRole     = $dom->createElement('PublishingDateRole','01');
			
			/*	
				00 : YYYYMMDD 
				14 : YYYYMMDDThhmmss. Alternatively, the time may be suffixed with an optional ‘Z’ for UTC times, or with ‘+’ or ‘-’ and an hhmm timezone offset from UTC. (Ex. : 20120530T131243-0400)
			*/
			
			$data = date('Ymd');
			
			$DateFormat   			= $dom->createElement('DateFormat','00'); 
			$Date 		   			= $dom->createElement('Date',$data);
			
			$PublishingDate->appendChild($PublishingDateRole);
			$PublishingDate->appendChild($DateFormat);
			$PublishingDate->appendChild($Date);
			
			$PublishingDetail->appendChild($PublishingDate);
			
			
			
			
			$SalesRights = $dom->createElement('SalesRights');
			
			/**
				01 : For sale with exclusive rights in the specified country/ies
				03 : Not for sale in the specified country/ies. 
			**/
			
			$SalesRightsType = $dom->createElement('SalesRightsType','01');
			
			$SalesRights->appendChild($SalesRightsType);
			$RegionsIncluded = $dom->createElement('RegionsIncluded', 'WORLD');
			$TerritoryS = $dom->createElement('Territory');
			$TerritoryS->appendChild($RegionsIncluded);
			$SalesRights->appendChild($TerritoryS);
			
			/****Fine inizio dettagli pubblicazione...****/
			
			$PublishingDetail->appendChild($SalesRights);
			
			$product->appendChild($PublishingDetail);
			
			/****cartaceo collegato****/
			$RelatedMaterial = $dom->createElement('RelatedMaterial');
			$RelatedProduct = $dom->createElement('RelatedProduct');
			/**
				00 : Unspecified
				01 : Includes
				02 : Is part of
				06 : Alternative format.
				07 : Has ancillary product
				08 : Is ancillary to
				11 : Is remainder of
				12 : Publisher's suggested alternative
				13 : Epublication based on (print product).
				22 : Product by same author
				23 : Similar product
				28 : Enhanced version
				29 : Basic version available as
				30 : Product in same collection
				31 : Library product 
			
			**/
			
			$ProductRelationCode = $dom->createElement('ProductRelationCode','13');
			
			$RelatedProduct->appendChild($ProductRelationCode);
			
			$ProductIdentifier	 = $dom->createElement('ProductIdentifier');
			/**
				01 : Custom
				02 : ISBN 10
				03 : EAN
				15 : ISBN 13 
			
			**/
			$ProductIDType	= $dom->createElement('ProductIDType','03');
			$IDValue	 	= $dom->createElement('IDValue',$post['isbn_old']);
			
			$ProductIdentifier->appendChild($ProductIDType);
			$ProductIdentifier->appendChild($IDValue);
			
			$RelatedProduct->appendChild($ProductIdentifier);
			$RelatedMaterial->appendChild($RelatedProduct);
			
			/****fine cartaceo collegato****/
			
			$product->appendChild($RelatedMaterial);
			
			
			$ProductSupply = $dom->createElement('ProductSupply');
			$SupplyDetail = $dom->createElement('SupplyDetail');

			/**
				20 : Available
				40 : Not Available
			**/ 
			$ProductAvailability = $dom->createElement('ProductAvailability','20');
			
			$SupplyDetail->appendChild($ProductAvailability);
			$SupplyDate = $dom->createElement('SupplyDate');
			$SupplyDateRole = $dom->createElement('SupplyDateRole','08');
			$DateFormat = $dom->createElement('DateFormat','00');
			$Date = $dom->createElement('Date',$data);
			
			$SupplyDate->appendChild($SupplyDateRole);
			$SupplyDate->appendChild($DateFormat);
			$SupplyDate->appendChild($Date);
			
			$SupplyDetail->appendChild($SupplyDate);
	
			
			
			/******INIZIO PREZZO*****/
			
			/**EURO**/
			$price_euro = $post['prezzo'] - 0.01;
			
			$ProductSupply->appendChild($SupplyDetail);
			$Price 			= $dom->createElement('Price');
			$PriceType 		= $dom->createElement('PriceType','04');
			$PriceQualifier = $dom->createElement('PriceQualifier','05');
			$PriceStatus 	= $dom->createElement('PriceStatus','02');
			$PriceAmount 	= $dom->createElement('PriceAmount',str_replace('.',',',$price_euro));
			$CurrencyCode 	= $dom->createElement('CurrencyCode','EUR');
			
			
			$Price->appendChild($PriceType);
			$Price->appendChild($PriceQualifier);
			$Price->appendChild($PriceStatus);
			$Price->appendChild($PriceAmount);
			$Price->appendChild($CurrencyCode);
			$CountriesIncluded =$dom->createElement('CountriesIncluded', 'EU'); 
			$Territory = $dom->createElement('Territory');
			$Territory->appendChild($CountriesIncluded);
			
			$Price->appendChild($Territory);
			
			$ProductSupply->appendChild($Price);
			
			$PriceDate 			= $dom->createElement('PriceDate');
			$PriceDateRole 		= $dom->createElement('PriceDateRole','14');
			$DateFormat 		= $dom->createElement('DateFormat','14');
			$Date 				= $dom->createElement('Date',$data);
			
			$PriceDate->appendChild($PriceDateRole);
			$PriceDate->appendChild($DateFormat);
			$PriceDate->appendChild($Date);
			
			$Price->appendChild($PriceDate);
			
			/******FINE PREZZO IN EURO*****/
			$SupplyDetail->appendChild($Price);
			
			$ProductSupply->appendChild($SupplyDetail);
			
			$product->appendChild($ProductSupply);
			
			/**Per Ora no**/
			$prices = getCovertedPrice($price_euro);
			
			
			
			
			
			
			/**ALTRE VALUTE**/
			
			foreach($prices as $key => $price){
				
				
				
				$ProductSupply->appendChild($SupplyDetail);
				$Price 			= $dom->createElement('Price');
				$PriceType 		= $dom->createElement('PriceType','04');
				$PriceQualifier = $dom->createElement('PriceQualifier','05');
				$PriceStatus 	= $dom->createElement('PriceStatus','02');
				$PriceAmount 	= $dom->createElement('PriceAmount',$price);
				$CurrencyCode 	= $dom->createElement('CurrencyCode',$key);
				
				
				$Price->appendChild($PriceType);
				$Price->appendChild($PriceQualifier);
				$Price->appendChild($PriceStatus);
				$Price->appendChild($PriceAmount);
				$Price->appendChild($CurrencyCode);
				
				$Territory = $dom->createElement('Territory');
				if($key === 'USD'){
					
					$cCode = 'US';
				}
				
				if($key === 'GBP'){
					
					$cCode = 'GB';
				}
				if($key === 'CHF'){
					
					$cCode = 'CH';
				}
				
				
				$CountriesIncluded =$dom->createElement('CountriesIncluded', $cCode); 
				$Territory->appendChild($CountriesIncluded);
				
				$Price->appendChild($Territory);
				
				$ProductSupply->appendChild($Price);
				
				$PriceDate 			= $dom->createElement('PriceDate');
				$PriceDateRole 		= $dom->createElement('PriceDateRole','14');
				$DateFormat 		= $dom->createElement('DateFormat','00');
				$Date 				= $dom->createElement('Date',$data);
				
				$PriceDate->appendChild($PriceDateRole);
				$PriceDate->appendChild($DateFormat);
				$PriceDate->appendChild($Date);
				
				$Price->appendChild($PriceDate);
				
				/******FINE PREZZO ALTRE VALUTE*****/
				$SupplyDetail->appendChild($Price);
			
			}
			
			//$product->appendChild($SupplyDetail);
			
			
			$ONIXMessage->appendChild($product);
			
						
			$dom->appendChild($ONIXMessage);
			
			$dir_class = new Dir();
			$folder_name = $isbn;
			$target_dir = _PATH_.'edigita/' .$folder_name. '/';
			
			$status_dir = $dir_class->create_folder($target_dir);
			
			
			/**
				Crea La catella 
				se non isiste 
				se esiste elimina contenuto
			**/
			
			/*if($status_dir ){
				
				$dir_class->delete_folder($target_dir);
				
			}*/
			
			
			$filePath = $target_dir. $isbn.'.xml';
			
			
			

			
			/**
				
				Salva file xml
				
			**/
			
			
			if($dom->save($filePath)){
				
				return true;
					
				
			}else{
				
				return false;
			}
			
			
		}
		
	/***EBOOK CARICATO IN EDIGITA***/
	
	public function get_ebook_uploaded($isbn){
		

		//HTTP edigita user
		$username = $this->user;
		//HTTP edigita user.
		$password = $this->password;
		//headers array.
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Basic '. base64_encode("$username:$password")
		);
			
			
		//echo "https://edigita.cantook.net/api/organisations/1308/publications/". $isbn . ".xml<br />";
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://edigita.cantook.net/api/organisations/1308/publications/". $isbn . ".xml",
			CURLOPT_HTTPHEADER =>$headers,
			CURLOPT_RETURNTRANSFER => 1
		));
		
		
		$response = curl_exec($curl);
		
		$xmlresp = new SimpleXMLElement($response);
       
		
	
		
		

		if($xmlresp->code== 404){
			
			return false;
		}else{
			return true;
		}
		
		curl_close();
		
		
	}
	
	public function get_ebook_uploaded_detail($isbn){
		

		//HTTP edigita user
		$username = $this->user;
		//HTTP edigita user.
		$password = $this->password;
		
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Basic '. base64_encode("$username:$password")
		);
			
			
		//echo "https://edigita.cantook.net/api/organisations/1308/publications/". $isbn . ".xml<br />";
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://edigita.cantook.net/api/organisations/1308/publications/". $isbn . ".xml",
			CURLOPT_HTTPHEADER =>$headers,
			CURLOPT_RETURNTRANSFER => 1
		));
		
		
		$response = curl_exec($curl);
		
		$xmlresp = new SimpleXMLElement($response);
       
		
	
		
		

		if($xmlresp->code== 404){
			
			return $xmlresp;
		}else{
			return $xmlresp;
		}
		
		curl_close();
		
		
	}
	

}
?>
