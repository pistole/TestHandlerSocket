<?php
require_once('DbWrapper.class.php');

class TestDataCreator
{
	
	protected $sizes = array();
	
	public function __construct($sizes)
	{
		$this->sizes = $sizes;
	}

	protected function randString($length = 256)
	{
		$retval = '';
		for ($i = 0; $i < $length; $i += 32)
		{
			$retval .= md5(mt_rand());
		}
		return substr($retval, 0, $length);
	}

	protected function createTestDataForSize($size) 
	{
		$rows = (int) $size;
		$db = DbWrapper::getInstance();
		$db->selectDb('test_handlersocket');
		$db->query('DROP TABLE IF EXISTS `test_data_'.$rows.'`');
		$db->query('
			CREATE TABLE `test_handlersocket`.`test_data_'.$rows.'` (
			`test_data_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`name` CHAR(256),
			`num` BIGINT(20),
			`data` CHAR(1024)
			) ENGINE = InnoDB;
		');

		$chunkSize = 20000;

		$numChunks = ceil($rows / $chunkSize);
		for ($j = 0; $j < ($numChunks) ; $j++)
		{
			$rowsArry = array();
			$chunkCount = min($chunkSize, $rows - ($j * $chunkSize));
			for ($i = 0; $i < $chunkCount; $i++)
			{
				$row = array(
					$db->quote($this->randString(200)),
					rand(),
					$db->quote($this->randString(1000)),
					);

				$rowsArry[] = '(' . implode(', ', $row ) . ')';

			}
			print_r($chunkCount . "\n");
			print_r(count($rowsArry) . "\n");
			print_r($numChunks . "\n");
			print_r($j . "\n");
			$sql = 'INSERT INTO test_data_'.$rows.' (`name`, `num`, `data`) VALUES ' . implode(",\n", $rowsArry) . '; ';
			$db->query($sql);
		}
	}

	public function go()
	{
		foreach ($this->sizes as $size)
		{
			$this->createTestDataForSize($size);
			echo "created for size: " . $size . "\n";
		}
	}


	
}