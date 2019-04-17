<?php

// curl request for URL
class DataHandling {
	public static function fetch( $url ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		$result = curl_exec( $ch );
		curl_close( $ch );

		return $result;
	}
}

class InstagramFetch {

	public $result;
	public $error = false;

	public function __construct( $count = 30 ) {

		// this will set the option to be the propeller manager social page
		$option = get_field('social_media_settings', 'option');
		
		// set the access token to be token field of the option
		$this->access_token = $option['instagram_token'];

		// pass the count - max is 33 results so round down to 30
		$this->count      = $count;
		// if pagination link exists add it to url
		$this->pagination = isset( $_POST['next_max_id'] ) ? '&max_id=' . $_POST['next_max_id'] : '';

		try {

			// build the url
			$url = $this->get_searchurl( $_POST['searchtype'], $_POST['searchinput'] );

			// json decode result
			$this->result = json_decode( DataHandling::fetch( $url ) );

			// get any error or return result
			if ( isset( $this->result->meta->error_message ) ) {
				$error = new Exception($this->result->meta->error_message, $this->result->meta->code, null);
				throw $error;
			} else {
				$this->result;
			}

		} catch ( Exception $e ) {
			$this->error = $e;
		}

	}

	// create url based on posted data
	protected function get_searchurl( $searchtype, $searchinput ) {

		if( $searchtype == 'owner'){
			// fetch based on access token
			return sprintf( 'https://api.instagram.com/v1/users/self/media/recent?access_token=%s&count=%s%s', $this->access_token, $this->count, $this->pagination );
		}
		elseif ( $searchtype == 'users' && isset($_POST['checktype']) && $_POST['checktype'] == 'users' ) {
			// searching usernames
			return sprintf( 'https://api.instagram.com/v1/%s/%s/media/recent/?access_token=%s&count=%s', $searchtype, $searchinput, $this->access_token, $this->count );

		} else {
			// search hastags, selected users or locations
			return sprintf( 'https://api.instagram.com/v1/%s/%s/media/recent?access_token=%s&count=%s%s', $searchtype, $searchinput, $this->access_token, $this->count, $this->pagination );
		}

	}

}
