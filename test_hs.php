#!/usr/bin/env php
<?php

require_once('TestDataCreator.class.php');
require_once('TestBenchmark.class.php');



$sizes = array(200, 2000, 20000, 200000, 2000000);
$count = 10000;
$createData = false;
$doBench = true;

foreach ($argv as $arg)
{
	if (strtolower($arg) == '--create')
	{
		$createData = true;
	}
	
	if (strpos(strtolower($arg), '--count=') !== false)
	{
		
		$count = (int) substr($arg, 8);
	}
	
}


if ($createData)
{
	$data = new TestDataCreator($sizes);
	$data->go();
}

$benchmark = new TestBenchmark($sizes, $count);
$benchmark->go();