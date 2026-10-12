<?php

namespace WPML\Forms\NinjaForms\SharedAPI;

use WPML\Forms\Loader\AddonStatus;

class Status implements AddonStatus {

	const INIT_ACTION = 'nf_init';

	/**
	 * Checks if Add-On is active.
	 *
	 * @return bool
	 */
	public function isActive() {
		return (bool) did_action( self::INIT_ACTION );
	}
}
