<?
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful
// require 'scraperwiki.php';
// require 'scraperwiki/simple_html_dom.php';
//
// // Read in a page
// $html = scraperwiki::scrape("http://foo.com");
//
// // Find something on the page using css selectors
// $dom = new simple_html_dom();
// $dom->load($html);
// print_r($dom->find("table.list"));
//
// // Write out to the sqlite database using scraperwiki library
// scraperwiki::save_sqlite(array('name'), array('name' => 'susan', 'occupation' => 'software developer'));
//
// // An arbitrary query against the database
// scraperwiki::select("* from data where 'name'='peter'")
// You don't have to do things with the ScraperWiki library.
// You can use whatever libraries you want: https://morph.io/documentation/php
// All that matters is that your final data is written to an SQLite database
// called "data.sqlite" in the current working directory which has at least a table
// called "data".
?>


<?php
require 'scraperwiki.php';

$endtime = time() + (60*60) * 23; //23h 

for ($id = 1827028; $id <= 2100000; $id++) {
	if ($id != 1576683)
	{
	if ($endtime <= time())
	{
		exit;
	}
	$i = 1;
	$delay = 250000;
	  if (!validateEntry($id))
	  {
	  print $id;
	  while (!validateEntry($id))
	  {
	    print ".";
	  	$delay = $delay + $i * 250000;
	  	//limit to 5 secs
	  	if ($delay > 5000000) {
	  		$delay = 5000000;
	  	}
	  	if ($i % 20 == 0)
	  	{
	  		$delay = 60000000;
	  	}
	  	if ($i == 61)
	  	{
	  		exit;
	  	}
	    usleep($delay);
	    ripById($id);
	    $i++;
	  }
	  print "!";
	  }
  }
}
function ripById($id){
	$pathToDetails = 'http://beheshtezahra.tehran.ir/Default.aspx?tabid=92&ctl=SearchDetails&mid=653&srid=' . $id;
	
	$output = scraperwiki::scrape($pathToDetails);
	$firstnamepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblNameBound_0"><b>(.*)<\//smiU';
	$surnamepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblLastNameBound_0"><b>(.*)<\//smiU';
	$fathernamepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblFatherNameBound_0"><b>(.*)<\//smiU';
	$birthdatepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblBirthDateBound_0"><b>(.*)<\//smiU';
	$deathdatepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblDafnDateBound_0"><b>(.*)<\//smiU';
	$deathplacepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblDeastTownshipTitle_0"><b>(.*)<\//smiU';
	$graveplacepattern = '/<span id="dnn_ctr653_SearchDetails_dtlDetail_lblDafnPlace_0"><b>(.*)<\//smiU';
	
		
        preg_match($firstnamepattern, $output, $temp);
      	$firstname = (isset($temp[1])) ? $temp[1] : '';
        
        preg_match($surnamepattern, $output, $temp);
        $surname = (isset($temp[1])) ? $temp[1] : '';
        
        preg_match($fathernamepattern, $output, $temp);
        $fathername = (isset($temp[1])) ? $temp[1] : '';
        
        preg_match($birthdatepattern, $output, $temp);
        $birthdate = (isset($temp[1])) ? $temp[1] : '';
        
        preg_match($deathdatepattern, $output, $temp);
        $deathdate = (isset($temp[1])) ? $temp[1] : '';
        
        preg_match($deathplacepattern, $output, $temp);
        $deathplace = (isset($temp[1])) ? $temp[1] : '';
        
        preg_match($graveplacepattern, $output, $temp);
        $graveplace = (isset($temp[1])) ? $temp[1] : '';
        
        
	scraperwiki::save_sqlite(array('data'), 
	                    array(
	                          'id'      => $id,
	                          'firstname' => $firstname,
	                          'surname' => $surname, 
	                          'fathername' => $fathername, 
	                          'birthdate' => $birthdate, 
	                          'deathdate' => $deathdate,
	                          'deathplace' => $deathplace, 
	                          'graveplace' => $graveplace));
}
function validateEntry($id){
	$result = false;
	// Set total number of rows
	try {
	$recordSet = scraperwiki::select("* from data where id ='". $id . "'");
	if (!empty($recordSet[0]['id'])) {
		if ($recordSet[0]['surname'] != ""){
			$result = true;	
		}
		if ($recordSet[0]['firstname'] != ""){
			$result = true;	
		}
		if ($recordSet[0]['fathername'] != ""){
			$result = true;	
		}
	} 
	} catch (Exception $e) {
	}
	return $result;
}
