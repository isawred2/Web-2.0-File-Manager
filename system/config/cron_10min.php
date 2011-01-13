<?
/****************************************************************
*
*  This is a cron file that should be run every 10 minutes
*
*****************************************************************/

print("Script Run At: ".date("h:m a")."\n");

$dir = dirname(__FILE__);
require($dir."/cnf_system.php");
require($dir."/../includes/adodb/adodb.inc.php");

$db = NewADOConnection($sys_dbType);
$db->connect($sys_dbIP, $sys_dbLogin, $sys_dbPass, $sys_dbName);
$db->debug = true;

// =============================================
// ----- clean sessions, count users

$sql = "DELETE FROM $sys_schema.sys_session WHERE ses_expire < current_timestamp;
		SELECT count(1) FROM $sys_schema.sys_session;";
$rs  = $db->execute($sql);
$users = $rs->fields[0];

// =============================================
// ---------- get transfers

exec("/sbin/ifconfig", $output, $res);
$info = implode("<br>", $output);
	
$pos1 = strpos($info, "RX bytes:");
$pos2 = strpos($info, " ", $pos1+9);
$rx   = substr($info, $pos1+9, $pos2-$pos1-9);

$pos1 = strpos($info, "TX bytes:");
$pos2 = strpos($info, " ", $pos1+9);
$tx   = substr($info, $pos1+9, $pos2-$pos1-9);

unset($output);

// =============================================
// ---------- get loads

exec("/usr/bin/uptime", $output, $res);
$info = implode("<br>", $output);

$pos1 = strpos($info, "load average:");
$tmp  = substr($info, $pos1+13);
$tmp  = split(",", $tmp);
$load = trim($tmp[1]);

// =============================================
// ---------- save info the database
	
$sql = "SELECT rx_bytes, tx_bytes FROM $sys_schema.log_load ORDER BY loadid DESC LIMIT 1";
$rs  = $db->execute($sql);
if ($rs && !$rs->EOF) { $rx_prev = $rs->fields[0]; $tx_prev = $rs->fields[1]; } else { $rx_prev = 0; $tx_prev = 0; }

$rx_new = $rx - $rx_prev ;
$tx_new = $tx - $tx_prev;
if ($rx_new < 0) $rx_new = 0;
if ($tx_new < 0) $tx_new = 0;

$sql = "INSERT INTO $sys_schema.log_load (rx_bytes, rx_dif, tx_bytes, tx_dif, load_average, load_users)
		VALUES($rx, $rx_new, $tx, $tx_new, $load, $users)";
$db->execute($sql);
?>