<?php
require_once('DbWrapper.class.php');
require_once('Net/HandlerSocket.php');

class TestBenchmark
{
	protected $sizes = array();
	protected $count = 10000;
	
	public function __construct($sizes, $count)
	{
		$this->sizes = $sizes;
		$this->count = $count;
	}
	
	protected function testSql($key, $forceNew = false, $size)
	{
		$db = DbWrapper::getInstance($forceNew);
		if ($forceNew)
		{
			$db->selectDb('test_handlersocket');
		}
		$sql = '
			SELECT SQL_NO_CACHE
				*
			FROM 
				test_data_'.(int) $size.'
			WHERE
				test_data_id = ' . $db->quote($key) . ' 
		';
	
		$result = $db->query($sql);
		while ($row = $result->getRow())
		{
			return $row;
		}
	}

	protected function testHs($key, $forceNewHs = false, $size)
	{
		static $hs = null;
		$fieldNames = array('test_data_id', 'name', 'num', 'data');

		if (is_null($hs) || $forceNewHs)
		{
			$hs = new HandlerSocket('localhost', '9998');
			$dbName = 'test_handlersocket';
			$tableName = 'test_data_' . (int) $size;

			$hs->openIndex(0, $dbName, $tableName, 'PRIMARY', $fieldNames);

		}

		$r = $hs->executeSingle(0, '=', array($key), 1, 0);
		if (is_array($r) && count($r) > 2)
		{
			array_pop($r);
			array_pop($r);
		}

		return $r;

	}




	protected function benchmark($size = 200, $count = 10000)
	{
		echo "count " . $count . "\n";
		$startTime = microtime(true);
		for ($i = 0; $i < $count; $i++)
		{ 
		
			$row = $this->testSql(rand(1, $size), ($i == 0), $size);
		}
		$endTime = microtime(true);

		echo($endTime - $startTime) . ' sql total time in sec.' . "\n";


		$startTime = microtime(true);
		for ($i = 0; $i < $count; $i++)
		{
			$row = $this->testHs(rand(1, $size), ($i == 0), $size);
		}
		$endTime = microtime(true);

		echo($endTime - $startTime) . ' hs total time in sec.' . "\n";

	}
	
	public function go()
	{
		foreach ($this->sizes as $size)
		{
			echo "testing size: " . $size . "\n";
			$this->benchmark($size, $this->count);
		}
		
	}
	
}