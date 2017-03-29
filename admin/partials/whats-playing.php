<form action='options.php' method='post'>

	<h2>Whats Playing</h2>

	<?php
		settings_errors();
		settings_fields( 'whats_playing' );
		do_settings_sections( 'whats_playing' );
		submit_button('Authenticate');
	?>
</form>