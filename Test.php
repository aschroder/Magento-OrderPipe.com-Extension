<?php 

/**
 * Simple little test wrapper, run via commandline to check things are working.
 */


$url = "http://www.yourstore.com";
$user = "orderpipe_username";

set_include_path(get_include_path() . PATH_SEPARATOR . getcwd());
require_once 'app/Mage.php';

Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

umask(0);
Mage::app('default');

// Test directly
$observer = Mage::getModel('orderpipe/observer');
$observer->ping($url, $user, true); // debug = true, we get result logging

// Test via events
// Mage::dispatchEvent('sales_order_save_commit_after', array());