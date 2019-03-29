<?php
require 'gapi.class.php';
$arr_pageviews = [];
$gaEmail = 'phuc-79@gg-ana.iam.gserviceaccount.com';
$gaPassword = 'gg-ana-4b9d5c161c69.p12';
$profileId = '94883462';

$dimensions = array('pagePath'); 
$metrics = array('pageviews');
$sortMetric=null;

// $filter = 'pagepath=~3155824.html || pagepath=~3146791.html || pagepath=~3156169.html || pagepath=~3142828.html || pagepath=~3147556.html || pagepath=~3150358.html || pagepath=~3147796.html || pagepath=~3146746.html || pagepath=~3146440.html || pagepath=~3145333.html || pagepath=~3140095.html || pagepath=~3064429.html ';
$filter = 'pagepath=~3486883.html';

$ga = new gapi($gaEmail, $gaPassword);

$ga->requestReportData($profileId, $dimensions, $metrics, $sortMetric, $filter);
$result = $ga->getResults();
// var_dump($result);
foreach ($result as $value) {
	$metric = $value->getMetrics();
	$dimension = $value->getDimensions();
	$metric = (object) $metric;
	$dimension = (object) $dimension;
	$pageviews = $metric->pageviews;
	$pagepath = $dimension->pagepath;

	$pos = strrpos($pagepath, ".");
	$xoa_tu_html = substr($pagepath,0,$pos);
	$id_from_pagepath = substr($xoa_tu_html, -6);

	echo '<h3>'.$pagepath.'</h3>';
	// echo '<br>';
	echo '<h3>'.$pageviews.'</h3>';
	echo '<br>';
	
}
