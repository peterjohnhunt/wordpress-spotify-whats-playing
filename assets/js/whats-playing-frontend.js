jQuery(document).ready(function($){

//░░░░░░░░░░░░░░░░░░░░░░░░
//
//	 DIRECTORY
//
//	 _Open
//	 _Refresh
//	 _Load
//
//░░░░░░░░░░░░░░░░░░░░░░░░

var $whatsPlaying = $('#whats-playing');

if( $whatsPlaying[0] ){
    var refresh;

//▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀
// _Open
//▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄

$whatsPlaying.find('.bubbles').click(function(){
    $whatsPlaying.toggleClass('open');
});


//▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀
// _Refresh
//▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄

$whatsPlaying.on('load refresh', function(){
    $.post(WHATS_PLAYING.ajax.url, {
        action: 'get_whats_playing',
        nonce: WHATS_PLAYING.ajax.nonce
    }, function(response){
        if( response.success ){
            $whatsPlaying.find('.wrapper').html(response.data.html);
            $whatsPlaying.addClass('loaded');
        } else {
            if( refresh ){
                clearInterval(refresh);
            }
        }
    });
});


//▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀
// _Load
//▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄

$(window).load(function(){
    $whatsPlaying.trigger('load');

    refresh = setInterval(function(){
        $whatsPlaying.trigger('refresh');
    }, 60000);
});

}
});