<?php

namespace PageViewsProperty;

use SMW\DIProperty;
use SMW\SemanticData;
use SESP\AppFactory;
use SMWDINumber;
use GoogleAnalyticsMetricsHooks;

class PageViewsProperty {

	/**
	 * @param AppFactory $appFactory
	 * @param DIProperty $property
	 * @param SemanticData $semanticData
	 */

	public static function addAnnotation($appFactory, $property, $semanticData ) {

		$page = $appFactory->newWikiPage( $semanticData->getSubject()->getTitle() );
		$count = self::getPageViewCount( $page );

		if(is_numeric($count)) {
			return new SMWDINumber($count);
		} else {
			return null;
		}
	}

	private static function getPageViewCount( $page ) {
		return GoogleAnalyticsMetricsHooks::getMetricwithTitle($page->getTitle(), 'pageviews', '2005-01-01', 'today');
	}
}

