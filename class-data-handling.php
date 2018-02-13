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
	const ACCESS_TOKEN = '414143281.e2a9043.c4fd9115f0ab479da4122b0ddfe25169';

	public function __construct( $count = 30 ) {

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

		if ( $searchtype == 'users' && isset($_POST['checktype']) && $_POST['checktype'] == 'users' ) {
			// searching usernames
			return sprintf( 'https://api.instagram.com/v1/%s/search?q=%s&access_token=%s&count=%s', $searchtype, $searchinput, static::ACCESS_TOKEN, $this->count );
		} else {
			// search hastags, selected users or locations
			return sprintf( 'https://api.instagram.com/v1/%s/%s/media/recent?access_token=%s&count=%s%s', $searchtype, $searchinput, static::ACCESS_TOKEN, $this->count, $this->pagination );
		}

	}

}
