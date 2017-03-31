<div class="wrapper">
    <?php if ($profile): ?>
        <?php
        $user_name = $profile->display_name ? $profile->display_name : $profile->id;
        $user_href = $profile->uri ? ' href="'.$profile->uri.'" target="_blank"' : '';
        $user_pic  = !empty($profile->images) ? ' style="background-image:url('.$profile->images[0]->url.');"' : '';
        ?>
        <a class="profile"<?php echo $user_href; ?><?php echo $user_pic; ?>>
            <?php echo substr($user_name, 0, 1); ?>
        </a>
    <?php endif; ?>
    <?php if ($playing): ?>
        <?php
        $song_name = $playing->track->name;
        $song_href = $playing->track->uri ? ' href="'.$playing->track->uri.'" target="_blank"' : '';
        $artist = !empty($playing->track->artists) ? $playing->track->artists[0] : false;
        $artist_name = $artist ? $artist->name : '';
        $artist_href = $artist ? ' href="'.$artist->uri.'" target="_blank"' : '';
        $playlist = $playing->context->type;
        $playlist_href = $playing->context->uri ? ' href="'.$playing->context->uri.'" target="_blank"' : '';
        ?>
        <div class="track">
            <a class="song"<?php echo $song_href; ?>>
                <?php echo $song_name; ?>
            </a>
            <span class="meta">
                <a class="artist"<?php echo $artist_href; ?>>
                    <?php echo $artist_name; ?>
                </a> | <a class="playlist"<?php echo $playlist_href; ?>>
                    <?php echo $playlist; ?>
                </a>
            </span>
        </div>
        <div class="bars">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    <?php elseif ($profile): ?>
        <a class="follow"<?php echo $user_href; ?>>
            Follow Me on Spotify!
        </a>
    <?php endif; ?>
</div>