<?
/****************************************************************
*
*  This is a cron file that should be run every night
*
*****************************************************************/

$dir = dirname(__FILE__);

require($dir."/cnf_system.php");
require($dir."/../includes/adodb/adodb.inc.php");
require($dir."/../libs/phpStatistics.php");

// summarize statistics
if ($ws_systemDB) {
	$db = NewADOConnection($sys_dbType);
	$db->connect($sys_dbIP, $sys_dbLogin, $sys_dbPass, $sys_dbName);
	$ws_schema = $sys_schema;
} else {
	$ws_db = NewADOConnection($ws_dbType);
	$ws_db->connect($ws_dbIP, $ws_dbLogin, $ws_dbPass, $ws_dbName);
}

$sys_stats = new phpStatistics($db);
$sys_stats->clean();
?>