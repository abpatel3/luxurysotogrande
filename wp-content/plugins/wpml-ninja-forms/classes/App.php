<?php

namespace WPML\Forms\NinjaForms;

final class App {

	public static function init() {
		( new \WPML_Action_Filter_Loader() )->load( [
			\WPML\Forms\NinjaForms\SharedAPI\SharedBaseHooks::class,
			\WPML\Forms\NinjaForms\TranslationEditor\JobFilter::class,
		] );
	}
}
