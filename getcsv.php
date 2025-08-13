<?php
	$file = fopen("import.csv","r");
	while(! feof($file))
	{	
  		print_r(fgetcsv($file));
  	}
	fclose($file);
?>
