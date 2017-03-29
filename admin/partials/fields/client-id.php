<?php
    $id        = 'whats_playing_client_id';
    $options   = get_option( 'whats_playing_settings' );
    $client_id = isset($options[$id]) ? $options[$id] : '';
?>
<input type='text' name='whats_playing_settings[<?php echo $id; ?>]' value='<?php echo $client_id; ?>'>