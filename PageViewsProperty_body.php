<?php

use SMW\DIProperty;
use SMW\SemanticData;
use SESP\AppFactory;

class PageViewsProperty {

	/**
	 * @param AppFactory $appFactory
	 * @param DIProperty $property
	 * @param SemanticData $semanticData
	 */

	public static function addAnnotation($appFactory, $property, $semanticData ) {

		$page = $appFactory->newWikiPage( $semanticData->getSubject()->getTitle() );
		$count = self::getPageViewCount( $page );

		return new SMWDINumber($count);
	}

	private static function getPageViewCount( $page ) {
		return GoogleAnalyticsMetricsHooks::getMetricwithTitle($page->getTitle(), 'pageviews', '2005-01-01', 'today');
	}
}

