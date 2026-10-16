<?php
global $oc_common;

$oc_common['notification_email']=SUPPORT_EMAIL;
$oc_common['inventory_url'] = get_site_url().'/shop/';
$oc_common['white_inventory_url'] = get_site_url().'/shop/';
$oc_common['color_inventory_url'] = get_site_url().'/lab-created-colored-diamonds/';

global $oc_cut,$oc_color,$oc_clarity,$oc_certificate;

$oc_cut = array(
    1 => "Good",
    2 => "Very Good",
    3 => "Excellent",
    4 => "Ideal"
);
$oc_color = [
    1  => "yellow",
    2  => "pink",
    3  => "purple",
    4  => "red",
    5  => "blue",
    6  => "green",
    7  => "orange",
    8  => "brown",
    9  => "black",
    10 => "gray",
];
$oc_clarity = [
    1  => "I2",
    2  => "I1",
    3  => "SI3",
    4  => "SI2",
    5  => "SI1",
    6  => "VS2",
    7  => "VS1",
    8  => "VVS2",
    9  => "VVS1",
    10 => "IF",
];
$oc_certificate = [
	1  => "IGI",
	2  => "GIA",
	3  => "GCAL",
] ;
