<?php
namespace PageViewsProperty;

use OutputPage;
use Skin;
use JobQueueGroup;

class Hooks {

	public static function onExtension() {

		global $sespSpecialProperties, $sespLocalPropertyDefinitions;

		//add property annotator to SESP
		$sespSpecialProperties[] = '_GOOGLE_ANALYTICS_VIEWS';

		$sespLocalPropertyDefinitions['_GOOGLE_ANALYTICS_VIEWS'] = [
		    'id'    => '___GOOGLE_ANALYTICS_VIEWS',
		    'type'  => '_num',
		    'alias' => 'google-analytics-views-annotation-prop',
		    'label' => 'Google Analytics Views',
		    'callback'  => [ 'PageViewsProperty\PageViewsProperty', 'addAnnotation' ]
		];
	}

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {

		$jobParams = array();

		$title = $skin->getTitle();

		if($title){
			$job = new PageViewsPropertyUpdateJob( $title, $jobParams );
			JobQueueGroup::singleton()->push( $job ); // mediawiki >= 1.21
		}
	}


}