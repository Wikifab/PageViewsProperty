<?php

namespace PageViewsProperty;

use SMW\MediaWiki\Jobs\UpdateJob;
use RuntimeException;
use Job;
use DIWikiPage;
use WikiPage;
use Title;

class PageViewsPropertyUpdateJob extends Job {

	public function __construct( $title, $params ) {	
		parent::__construct( 'PageViewsPropertyUpdate', $title, $params );
	}

	/**
	 * Execute the job
	 *
	 * @return bool
	 */
	public function run() {

		$title = $this->title;

		if ( $title instanceof WikiPage || $title instanceof DIWikiPage ) {
			$title = $title->getTitle();
		}

		if ( is_string( $title ) ) {
			$title = Title::newFromText( $title );
		}

		if ( !$title instanceof Title ) {
			throw new RuntimeException( 'Expected a title instance' );
		}
		
		$job = new UpdateJob( $title );
		$job->run();

		return true;
	}
}