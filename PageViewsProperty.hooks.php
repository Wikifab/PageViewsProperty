<?php
namespace PageViewsProperty;

use OutputPage;
use Skin;
use JobQueueGroup;

class Hooks {

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {

		$jobParams = array();

		$title = $skin->getTitle();

		if($title){
			var_dump('Create Jobs');
			$job = new PageViewsPropertyUpdateJob( $title, $jobParams );
			JobQueueGroup::singleton()->push( $job ); // mediawiki >= 1.21
		}
	}
}