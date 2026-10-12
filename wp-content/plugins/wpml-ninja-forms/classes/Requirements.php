<?php

namespace WPML\Forms\NinjaForms;

class Requirements {

	public static function addNotice() {
		add_action( 'admin_notices', function() {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'WPML Ninja Forms is enabled but not effective. It requires a valid WPML version activated in order to work.', 'wpml-ninja-forms' ); ?></p>
			</div>
			<?php
		} );
	}
}
