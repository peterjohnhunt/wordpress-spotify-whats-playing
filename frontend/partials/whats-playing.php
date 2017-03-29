<?php
    $classes = array();
    if ($profile) {
        $classes[] = 'has-profile';
    }
    if ($playing) {
        $classes[] = 'is-playing';
    }
    $class = !empty($classes) ? ' class="'.implode($classes,' ').'"' : '';
?>
<aside id="whats-playing"<?php echo $class; ?>>
    <?php if ($profile): ?>
        <?php
            $user_name = $profile->display_name ? $profile->display_name : $profile->id;
            $user_href = $profile->uri ? ' href="'.$profile->uri.'" target="_blank"' : '';
            $user_pic  = !empty($profile->images) ? $profile->images[0]->url : '';
        ?>
        <a<?php echo $user_href; ?>>
            <?php if ($user_pic): ?>
                <img src="<?php echo $user_pic; ?>" alt="<?php echo "{$user_name} spotify profile picture" ?>">
            <?php else: ?>
                <span><?php echo $user_name; ?></span>
            <?php endif; ?>
        </a>
    <?php endif; ?>
    <?php if ($playing): ?>
        <?php
            $track_name = $playing->track->name;
            $track_href = $playing->track->uri ? ' href="'.$playing->track->uri.'" target="_blank"' : '';
        ?>
        <a<?php echo $track_href; ?>>
            <?php echo $track_name; ?>
        </a>
    <?php endif; ?>
</aside>