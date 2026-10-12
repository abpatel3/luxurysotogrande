<?php

namespace WPML\Forms\NinjaForms\SharedAPI;

class SharedBaseHooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action {

	public function add_hooks() {
		$forms = new \WPML\Forms(
			WPML_NINJA_FORMS_FILE,
			NinjaForms::class,
			new Status()
		);
		$forms->addHooks();
	}
}
