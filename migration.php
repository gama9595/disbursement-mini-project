<?php
class MigrateDatabase
{
	private $create_base_table_sql = "CREATE TABLE IF NOT EXISTS `tbl_sql_migration_logs` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `file_name` varchar(205) NOT NULL,
	  `migrated` int(1) DEFAULT 0,
	  `response` text DEFAULT NULL,
	  `migration_date` datetime NOT NULL,
	  PRIMARY KEY (`id`)
	)";
	private $check_exist_base_table_sql = "SELECT 1 FROM tbl_sql_migration_logs LIMIT 1";
	private $select_all_base_table_sql = "SELECT * FROM tbl_sql_migration_logs";
	private $insert_table_sql = "INSERT INTO `tbl_sql_migration_logs`(`file_name`, `response`, `migrated`, `migration_date`) VALUES ";
	private $hostname = '';
	private $database = '';
	private $username = '';
	private $password = '';
	private $dbObject = '';
	private $tableRecords = array();

	private function __checkSqlTable()
	{
		if (mysqli_query($this->dbObject, $this->check_exist_base_table_sql)) {
			printf("SUCCESS :- Table Found For Sql Migration. </br>");
			return true;
		} else {
			printf("WARNING :- Table Not Found For Sql Migration. So Creating New Table. </br>");
			if (mysqli_query($this->dbObject, $this->create_base_table_sql)) {
				printf("SUCCESS :- Table has been successfully created For Sql Migration. </br>");
				return true;
			} else {
				printf("ERROR :- Could not able to execute due to :- %s\n", mysqli_error($this->dbObject) . ' </br>');
				return false;
			}
		}
	}

	private function __selectAllRecords()
	{
		$result = $this->dbObject->query($this->select_all_base_table_sql);
		if ($result->num_rows > 0) {
			printf("SUCCESS :- Records Found in Sql Migration Table count is :-  %s. </br>", $result->num_rows);
			while ($data = $result->fetch_assoc()) {
				$this->tableRecords[] = $data['file_name'];
			}
		} else {
			printf("SUCCESS :- No Record Found In Sql Migration. </br>");
		}
	}

	function __construct($hostname, $username, $password, $database)
	{
		$this->hostname = $hostname ? $hostname : '';
		$this->username = $username ? $username : '';
		$this->password = $password ? $password : '';
		$this->database = $database ? $database : '';
		$this->dbObject = new mysqli($this->hostname, $this->username, $this->password, $this->database);

		if ($this->dbObject->connect_errno) {
			printf("ERROR :- Connect failed: %s\n", $this->dbObject->connect_error . ' </br>');
			printf("ERROR :- EXITING APP. </br>");
			exit(1);
		} else {
			printf("SUCCESS :- Connection Established successfully. </br>");
		}

		if ($this->__checkSqlTable()) {
			$this->__selectAllRecords();
		} else {
			printf("ERROR :- EXITING APP. </br>");
			exit(1);
		}
	}

	private function __sanitizeSql($contents)
	{
		$comment_patterns = array(
			'/\/\*.*(\n)*.*(\*\/)?/',
			'/\s*--.*\n/',
			'/\s*SET SQL_MODE.*\n/',
			'/\s*SET time_zone.*\n/',
			'/\s*#.*\n/',
		);
		$contents = preg_replace($comment_patterns, "\n", $contents);
		$statements = explode(";\n", $contents);
		$statements = preg_replace("/\s/", ' ', $statements);
		$statements = array_filter($statements);
		return $statements;
	}

	private function __executeQuery($command)
	{
		$return = array('status' => false, 'message' => 'Invalid Query');
		printf('INFO :- Query Started :- %s', $command);
		if (mysqli_query($this->dbObject, $command)) {
			$return = array('status' => true, 'message' => 'Query successfully Executed.');
		} else {
			$return = array('status' => false, 'message' => $this->dbObject->error);
		}
		return $return;
	}

	private function __migrateToDatabase($filePath, $sqlArray = array())
	{
		printf('Got Sql Array of Length (%s). </br>', count($sqlArray));
		$wholeMigrated = 1;
		$response = array();
		foreach ($sqlArray as $key => $sql) {
			$returnStatement = $this->__executeQuery($sql);
			$returnStatement['query'] = $sql;
			$response[] = $returnStatement;
			if (!$returnStatement['status']) {
				$wholeMigrated = 0;
			}
		}
		$response = mysqli_real_escape_string($this->dbObject, json_encode($response));
		$nowDate = date('Y-m-d H:i:s');
		$sql = $this->insert_table_sql . "('$filePath', '$response', $wholeMigrated, '$nowDate')";
		$this->__executeQuery($sql);
		printf('INFO :- All SQL Executed from the file %s.</br>', $filePath);
		return $wholeMigrated;
	}

	private function __processMigration($filePath)
	{
		$sqlData = file_get_contents($filePath);
		$sqlData = $this->__sanitizeSql($sqlData);
		return $this->__migrateToDatabase($filePath, $sqlData);
	}

	public function migrate($path)
	{
		printf("INFO :- Migration Process Starts at %s. </br>", date('Y-m-d H:i:s'));
		if (!is_dir($path) || !is_readable($path)) {
			printf("ERROR :- Argument should be a path to valid, readable directory (" . var_export($path, true) . " provided) . </br>");
			exit(1);
		}
		$sqlFileArray = glob("$path/*.sql");
		foreach ($sqlFileArray as $key => $sqlFile) {
			if (in_array($sqlFile, $this->tableRecords)) {
				unset($sqlFileArray[$key]);
			}
		}
		if (empty($sqlFileArray)) {
			printf("ERROR :- No New SQL File To Migrate in (" . var_export($path, true) . " provided) . </br>");
			exit(0);
		}
		usort($sqlFileArray, function ($a, $b) {
			return filemtime($a) - filemtime($b);
		});
		foreach ($sqlFileArray as $sql) {
			printf('INFO :- Got File %s & passing it to Migration Method. </br>', $sql);
			$status = $this->__processMigration($sql);
			printf('INFO :- For File %s migration has been completed with status %s . </br>',  $sql, $status);
			if (!$status)
				exit(1);
		}
		printf("SUCCESS :- Migration Process Completed at %s. </br>", date('Y-m-d H:i:s'));
	}
}

//check the Config File For Database credentials
/*
$configFile = require __DIR__ . '/api/app/settings.php';
$servername = $configFile['settings']['doctrine']['connection']['host'];
$username = $configFile['settings']['doctrine']['connection']['user'];
$password = $configFile['settings']['doctrine']['connection']['password'];
$dbname = $configFile['settings']['doctrine']['connection']['dbname'];
$sqlFolder = __DIR__ . '/sql';
$migrateObject = new MigrateDatabase($servername, $username, $password, $dbname);
$migrateObject->migrate($sqlFolder);*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flip2";
$sqlFolder = __DIR__ . '/sql';
$migrateObject = new MigrateDatabase($servername, $username, $password, $dbname);
$migrateObject->migrate($sqlFolder);
