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
		    'alias' => 'ga-views',
		    'label' => 'Google Analytics Views',
		    'callback'  => [ 'PageViewsProperty\PageViewsProperty', 'addAnnotation' ]
		];
	}

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
                global $wgReadOnly;

		$jobParams = array();

		$title = $skin->getTitle();

		if($title && ! $wgReadOnly){
			// TODO : this is brutal : we should not launch a Jobs at each request !!!
			$job = new PageViewsPropertyUpdateJob( $title, $jobParams );
			JobQueueGroup::singleton()->push( $job ); // mediawiki >= 1.21
		}
	}


}
