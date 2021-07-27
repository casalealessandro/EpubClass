<?php 

	

	$cmd = "java -jar epubcheck-4.0.2/epubcheck.jar -out  report.xhtml a.epub";
	
	shell_exec($cmd);


?>