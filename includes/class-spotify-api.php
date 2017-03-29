<?php

//░░░░░░░░░░░░░░░░░░░░░░░░
//
//     DIRECTORY
//
//     _Variables
//     _Setup
//     _Authentication
//     _Token
//     _Requests
//       ∟sub
//       ∟Handler
//       ∟Endpoints
//       ∟Data
//
//░░░░░░░░░░░░░░░░░░░░░░░░

namespace Whats_Playing\Includes;

class Spotify_API {
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Variables
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    protected $auth_url;

    protected $request_url;

    protected $access_token;


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Setup
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    public function __construct() {
        $this->auth_url     = 'https://accounts.spotify.com/authorize';
        $this->token_url    = 'https://accounts.spotify.com/api/token';
        $this->request_url  = 'https://api.spotify.com/v1/';
	}


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Authentication
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    public function is_authenticated(){
        return ($this->access_token);
    }

    public function authenticate($client_id){
        $args = array(
            'response_type' => 'code',
            'client_id'     => $client_id,
            'scope'			=> urlencode('user-read-recently-played user-read-private user-read-email'),
            'redirect_uri'  => home_url('/'),
        );

        $url = add_query_arg( $args, $this->auth_url );

        wp_redirect($url);

        exit;
    }


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Token
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    public function set_token($access_token){
        $this->access_token = $access_token;
    }

    public function get_token($client_id, $client_secret, $code){
    	$params = array(
    		'client_id'     => $client_id,
    		'client_secret' => $client_secret,
    		'grant_type'    => 'authorization_code',
    		'code'          => $code,
    		'redirect_uri'  => home_url('/'),
    	);

    	$request = wp_remote_post( $this->token_url, array( 'body' => $params ) );

    	if ( is_wp_error( $request ) || ( $request[ 'response' ][ 'code' ] !== 200 ) )  {
    		return;
    	}

    	$request_body = json_decode( wp_remote_retrieve_body($request) );

    	if ( isset( $request_body->access_token ) ) {
    		return $request_body->access_token;
    	}
    }


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Requests
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    //∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
    // ∟Handler
    //∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

    private function make_request($endpoint, $args=array(), $lifespan=DAY_IN_SECONDS){
        if ( $this->is_authenticated() ) {

            $url = trailingslashit($this->request_url) . $endpoint;

            $url = add_query_arg($args, $url);

    		$cache_key = "spotify::".$endpoint.'::'. md5( $url );

    		$cached = get_transient( $cache_key );

    		if ( $cached !== false ) {
    			return $cached;
    		} else {
    			$request = wp_remote_get($url, array( 'headers' => array('Authorization' => 'Bearer ' . $this->access_token)));

    			if ( is_wp_error( $request ) || ( $request[ 'response' ][ 'code' ] !== 200 ) )  {
    				return;
    			}

    			$request_body = json_decode( wp_remote_retrieve_body($request) );

    			if ( isset( $request_body ) ) {
    				$data = $request_body;
    				set_transient( $cache_key, $data, $lifespan );
    				return $data;
    			}
    		}
        }
    }

    //∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
    // ∟Endpoints
    //∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

    public function get_profile($args=array()){
        return $this->make_request('me', $args);
    }

    public function get_playlists($args=array()){
        return $this->make_request('me/playlists', $args);
    }

    public function get_recently_played($args=array()){
        return $this->make_request('me/player/recently-played', $args, MINUTE_IN_SECONDS);
    }

    //∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
    // ∟Data
    //∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

    public function get_playing_song(){
        $recent = $this->get_recently_played(array('limit' => 1));

        if (!empty($recent->items)) {
            $last_song  = current($recent->items);
            $timestamp  = $last_song->played_at;

            $played = strtotime($timestamp);
            $today  = time();
            $diff   = $today - $played;

            if ( $diff <= 600 ){
                return $last_song;
            }
        }
    }
}