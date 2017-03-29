<?php
    $id        = 'refresh_token';
    $options   = get_option( 'whats_playing_settings' );
    $refresh_token = isset($options[$id]) ? $options[$id] : '';
?>
<input type='password' disabled="disabled" name='whats_playing_settings[<?php echo $id; ?>]' value='<?php echo $refresh_token; ?>'>
