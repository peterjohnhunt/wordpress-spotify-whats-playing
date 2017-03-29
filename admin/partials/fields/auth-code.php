<?php
    $id        = 'whats_playing_auth_code';
    $options   = get_option( 'whats_playing_settings' );
    $auth_code = isset($options[$id]) ? $options[$id] : '';
?>
<input type='password' disabled="disabled" name='whats_playing_settings[<?php echo $id; ?>]' value='<?php echo $auth_code; ?>'>
