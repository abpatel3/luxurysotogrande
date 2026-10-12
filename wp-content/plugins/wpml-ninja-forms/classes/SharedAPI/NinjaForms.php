<?php

namespace WPML\Forms\NinjaForms\SharedAPI;

use WPML\Forms\Loader\Base;
use WPML\Forms\Translation\Factory;

class NinjaForms extends Base {

	/** Gets package slug. */
	protected function getSlug() {
		return 'ninja-forms';
	}

	/** Gets package title. */
	protected function getTitle() {
		return 'Ninja Forms';
	}

	/** Adds hooks. */
	protected function addHooks() {

		$factory = new Factory( $this->preferences );

		$strings = new Strings(
			$this->getSlug(),
			$this->getTitle(),
			$factory
		);
		$strings->addHooks();

		$notifications = new Notifications(
			$this->getSlug(),
			$this->getTitle(),
			$factory
		);
		$notifications->addHooks();

		$package_filter = new Package( $this->getSlug() );
		$package_filter->addHooks();
	}
}
