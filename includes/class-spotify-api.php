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

    protected $client_id;

    protected $client_secret;

    protected $access_token;

    protected $refresh_token;

    protected $redirect_uri;


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Setup
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    public function __construct() {
        $this->auth_url     = 'https://accounts.spotify.com/authorize';
        $this->token_url    = 'https://accounts.spotify.com/api/token';
        $this->request_url  = 'https://api.spotify.com/v1/';
        $this->redirect_uri = force_ssl_admin() ? home_url('/','https') : home_url('/','http');

        $options = get_option('whats_playing_settings');

        $this->client_id     = isset($options['client_id']) ? $options['client_id'] : '';
        $this->client_secret = isset($options['client_secret']) ? $options['client_secret'] : '';
        $this->refresh_token = isset($options['refresh_token']) ? $options['refresh_token'] : '';

        if ( !$this->can_authenticate() ) {
            $this->delete_tokens();
        }
	}


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Authentication
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    public function can_authenticate(){
        return ($this->client_id && $this->client_secret);
    }

    public function is_authenticated(){
        return ($this->client_id && $this->client_secret && $this->refresh_token);
    }

    public function authenticate(){
        if ($this->can_authenticate()) {
            $args = array(
                'response_type' => 'code',
                'client_id'     => $this->client_id,
                'scope'			=> urlencode('user-read-recently-played user-read-private user-read-email'),
                'redirect_uri'  => $this->redirect_uri,
            );

            $url = add_query_arg( $args, $this->auth_url );

            wp_redirect($url);

            exit;
        }
    }


    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
    // _Token
    //≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

    public function request_tokens($params){
        $request = wp_remote_post( $this->token_url, array( 'headers' => array('Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret)), 'body' => $params ) );

        if ( is_wp_error( $request ) || ( $request[ 'response' ][ 'code' ] !== 200 ) )  {
            return;
        }

        $request_body = json_decode( wp_remote_retrieve_body($request) );

        if (isset($request_body->refresh_token)) {
            $this->refresh_token = $request_body->refresh_token;
            $settings = get_option( 'whats_playing_settings' );
            $settings['refresh_token'] = $this->refresh_token;
            update_option('whats_playing_settings', $settings);
        }

        set_transient('whatsplaying::token', $request_body->access_token, $request_body->expires_in);

        return ($request_body->access_token);
    }

    public function save_tokens($code=false){
        $params = array(
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->redirect_uri,
        );

        return $this->request_tokens($params);
    }

    public function delete_tokens(){
        $this->refresh_token = false;
        delete_transient('whatsplaying::token');
        $settings = get_option( 'whats_playing_settings' );
        unset($settings['refresh_token']);
        update_option('whats_playing_settings', $settings);
    }

    public function get_token(){
        $token = get_transient('whatsplaying::token');
        if ( $token !== false ) {
            return $token;
        } else {
            $params = array(
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->refresh_token,
            );

            $this->request_tokens($params);

            return $this->refresh_token;
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

            $access_token = $this->get_token();

            $url = trailingslashit($this->request_url) . $endpoint;

            $url = add_query_arg($args, $url);

    		$cache_key = "whatsplaying::".$endpoint.'::'. md5( $url );

    		$cached = get_transient( $cache_key );

    		if ( $cached !== false ) {
    			return $cached;
    		} else {
    			$request = wp_remote_get($url, array('headers' => array('Authorization' => 'Bearer ' . $access_token)));

    			if ( is_wp_error( $request ) || ( $request[ 'response' ][ 'code' ] !== 200 ) ) {
                    $this->delete_tokens();
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

    private function get_details($href, $lifespan=DAY_IN_SECONDS){
        if ( $this->is_authenticated() ) {

            $access_token = $this->get_token();

    		$cache_key = "whatsplaying::details::" . md5( $href );

    		$cached = get_transient( $cache_key );

    		if ( $cached !== false ) {
    			return $cached;
    		} else {
    			$request = wp_remote_get($href, array( 'headers' => array('Authorization' => 'Bearer ' . $access_token)));

    			if ( is_wp_error( $request ) || ( $request[ 'response' ][ 'code' ] !== 200 ) ) {
                    $this->delete_tokens();
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