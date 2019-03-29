<?php
require 'gapi.class.php';
$gaEmail = 'phuc-79@gg-ana.iam.gserviceaccount.com';
$gaPassword = 'gg-ana-4b9d5c161c69.p12';
$profileId = '94883462';

$dimensions = array('source'); 
$dimensions2 = array('deviceCategory'); 
$metrics = array('pageviews');
$sortMetric='-pageviews';
$startDate = '';
$endDate = '';
$filter = '';

$ngay = $_GET['ngay'];
$thang = $_GET['thang'];

if(empty($ngay) || empty($thang)) return;

$endDate = '2018-'.$thang.'-'.$ngay;
$startDate = date( 'Y-m-d', strtotime( $endDate . ' -7 day' ) );

echo "<h3>".$startDate."</h3>";
echo "<h3>".$endDate."</h3>";

$pattern_google = '/oogle/';
$pattern_facebook = '/acebook IA/';
$pattern_baomoi = '/baomoi/';
$pattern_direct = '/direct/';

$view_gg = 0;
$view_fb = 0;
$view_baomoi = 0;
$view_direct = 0;

$ga = new gapi($gaEmail, $gaPassword);

$ga->requestReportData($profileId, $dimensions, $metrics, $sortMetric, $filter, $startDate, $endDate);
$result = $ga->getResults();

$tong = 0;

foreach ($result as $value) {

	$metric = $value->getMetrics();
	$dimension = $value->getDimensions();

	$metric = (object) $metric;
	$dimension = (object) $dimension;

	$pageviews = $metric->pageviews;
	$source = $dimension->source;
	$tong = $tong + $pageviews;

	if (preg_match($pattern_google, $source, $matches)) {
		$view_gg = $view_gg + $pageviews;
	}
	if (preg_match($pattern_baomoi, $source, $matches)) {
		$view_baomoi = $view_baomoi + $pageviews;
	}
	if (preg_match($pattern_facebook, $source, $matches)) {
		$view_fb = $view_fb + $pageviews;
	}
	if (preg_match($pattern_direct, $source, $matches)) {
		$view_direct = $view_direct + $pageviews;
	}

	// echo $source;
	// echo $pageviews;

}	
echo 'TONG CONG: '.number_format($tong); echo "<br>";
echo "Facebook IA: ".number_format($view_fb); echo "<br>";
echo "Google: ".number_format($view_gg); echo "<br>";
echo "Direct: ".number_format($view_direct); echo "<br>";
echo "Baomoi: ".number_format($view_baomoi); echo "<br>";

$view_khac = $tong - $view_fb - $view_gg - $view_direct - $view_baomoi;
echo "Nguon khac: ".number_format($view_khac); echo "<br>";



$view_pc = 0;
$view_mobi = 0;
$pattern_desktop = '/desktop/';
$pattern_tablet = '/tablet/';
$pattern_mobile = '/mobile/';


$ga2 = new gapi($gaEmail, $gaPassword);
$ga2->requestReportData($profileId, $dimensions2, $metrics, $sortMetric, $filter, $startDate, $endDate);
$result = $ga2->getResults();
$tong2 = 0;

foreach ($result as $value) {

	$metric = $value->getMetrics();
	$dimension = $value->getDimensions();

	$metric = (object) $metric;
	$dimension = (object) $dimension;

	$pageviews = $metric->pageviews;
	$deviceCategory = $dimension->deviceCategory;
	if (preg_match($pattern_desktop, $deviceCategory, $matches)) {
		$view_pc = $view_pc + $pageviews;
	}
	if (preg_match($pattern_tablet, $deviceCategory, $matches)) {
		$view_pc = $view_pc + $pageviews;
	}
	if (preg_match($pattern_mobile, $deviceCategory, $matches)) {
		$view_mobi = $view_mobi + $pageviews;
	}

}	

echo "PC: ".number_format($view_pc); echo "<br>";
$view_mobi = $view_mobi - $view_fb;
echo "Mobi: ".number_format($view_mobi); echo "<br>";

