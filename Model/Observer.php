<?php 

/**
* OrderPipe Magento integration by World Wide Access.
*
* @package    Aschroder_Orderpipe
* @author     Ashley Schroder (aschroder.com)
* @copyright 	@copyright Copyright 2012 World Wide Access
*/
class Aschroder_Orderpipe_Model_Observer {
	
	
	const PING_URL = 'orderpipe/settings/pingurl';
	const ORDERPIPE_URL = 'sales/orderpipe/url';
	const ORDERPIPE_USER = 'sales/orderpipe/user';
	
	public function saveAfter($observer) {
		
		// Older Magento versions have no commit event.
		if (version_compare(Mage::getVersion(), '1.4.0.0', '<')) {
			$this->saveAfterCommit($observer);
		}
	}
	
	public function saveAfterCommit($observer) {
		
		$url = Mage::getStoreConfig(self::ORDERPIPE_URL);
		$user = Mage::getStoreConfig(self::ORDERPIPE_USER);
		$this->ping($url, $user);
	}
	
	public function ping($url, $user, $debugMode = false) {

		if (!$url || !$user) {
			Mage::log("No url or user, cannot ping OrderPipe.com");
			return;
		}
		
		try {
			
			$curl = new Varien_Http_Adapter_Curl();
			$curl->setConfig(array(
			            'timeout'   => 2 // max 2 seconds
				));
			
			$params = http_build_query(array(
						'url' => $url,
						'user' => $user
				));
			
			$curl->write(Zend_Http_Client::GET, 
				Mage::getStoreConfig(self::PING_URL)."?$params");
			
			$data = $curl->read();
			$curl->close();
			
			if ($debugMode) {
				Mage::log("Ping result: \n" . print_r($data, true));
			}
			
		} catch (Exception $e) {
			// if the ping fails, we just log it and exit
			Mage::log("OrderPipe.com Ping failed: \n" . $e->getMessage());
		}
	}

}
