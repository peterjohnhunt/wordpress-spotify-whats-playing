jQuery(document).ready(function($){

//░░░░░░░░░░░░░░░░░░░░░░░░
//
//	 DIRECTORY
//
//	 _FunctionName
//
//░░░░░░░░░░░░░░░░░░░░░░░░

//▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀
// _FunctionName
//▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄

var $whatsPlaying = $('#whats-playing'),
    $bubbles      = $whatsPlaying.find('.bubbles');

$bubbles.click(function(){
    $whatsPlaying.toggleClass('open');
});

});