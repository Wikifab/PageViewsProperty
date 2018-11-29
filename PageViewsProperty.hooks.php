<?php
namespace PageViewsProperty;

use OutputPage;
use Skin;
use JobQueueGroup;
use SMWDINumber;

class Hooks {

	public static function onExtension() {

		global $sespSpecialProperties, $sespLocalPropertyDefinitions;

		//add property annotator to SESP
		$sespSpecialProperties[] = '_GOOGLE_ANALYTICS_VIEWS';

		$sespLocalPropertyDefinitions['_GOOGLE_ANALYTICS_VIEWS'] = [
		    'id'    => '___GOOGLE_ANALYTICS_VIEWS',
		    'type'  => '_num',
		    'alias' => 'ga-views',
		    'label' => 'Google Analytics Views',
		    'callback'  => [ 'PageViewsProperty\PageViewsProperty', 'addAnnotation' ]
		];
	}

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
                global $wgReadOnly;

		/*global $wgScriptPath, $wgArticlePath, $wgScript, $IP, $wgUploadDirectory, $wgServer, $wgUploadPath, $wgExtensionAssetsPath;

		var_dump($wgScriptPath);
		var_dump($wgArticlePath);
		var_dump($wgScript);
		var_dump($IP);
		var_dump($wgUploadDirectory);
		var_dump($wgServer);
		var_dump($wgUploadPath);
		var_dump($wgExtensionAssetsPath);*/

		$lastUpdated = self::getLastUpdated($out);
		if (is_null($lastUpdated) || ($lastUpdated && self::needsRefresh($lastUpdated)) ) {

			$jobParams = array();

			$title = $skin->getTitle();

			if($title){
				$job = new PageViewsPropertyUpdateJob( $title, $jobParams );
				JobQueueGroup::singleton()->push( $job ); // mediawiki >= 1.21
			}

			self::setLastUpdated($out);
		}
	}

	public static function setLastUpdated($output) {

		$wgMemc = wfGetMainCache();

		$key = $wgMemc->makeKey('views', 'property', 'lastupdated');

		$date = new \DateTime();

		if($wgMemc->get($key)){
			$wgMemc->set($key, $date->format('Y-m-d H:i:s'));
		}else{
			$wgMemc->add($key, $date->format('Y-m-d H:i:s'));
		}
	}

	public static function getLastUpdated($output) {

		$wgMemc = wfGetMainCache();

		$key = $wgMemc->makeKey('views', 'property', 'lastupdated');

		if($res = $wgMemc->get($key)){
			return $res;
		}else{
			return null;
		}
	}

	public static function needsRefresh( $lastUpdated ) {

		global $wgPageViewsPropertyRefreshDelayInterval;

		$needsRefresh = true;

		$lastUpdated = new \DateTime( $lastUpdated );
		$now = new \DateTime();

		if ($wgPageViewsPropertyRefreshDelayInterval) {

			try {
			    $refreshDelayInterval = new \DateInterval($wgPageViewsPropertyRefreshDelayInterval);

			    if ($lastUpdated->add($refreshDelayInterval) < $now) {
					$needsRefresh = true;
				} else {
					$needsRefresh = false;
				}
			} catch (Exception $e) {
			    echo "L'intervalle spécifié dans la variable globale ne correspond pas à un intervalle correct. Pour plus d'informations, http://www.lephpfacile.com/manuel-php/dateinterval.construct.php . \n";
			}

		} else {
			$needsRefresh = true;
		}

		return $needsRefresh;
	}


}
