<?php

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
	const ACCESS_TOKEN = '414143281.e2a9043.6d4acb839c38488f831d826bf29d32fe';


	public function __construct( $count = 30 ) {

		$this->count      = $count;
		$this->pagination = isset( $_POST['next_max_id'] ) ? '&max_id=' . $_POST['next_max_id'] : '';

		try {

			$url = $this->get_searchurl( $_POST['searchtype'], $_POST['searchinput'] );
			//var_dump($url);

			$this->result = json_decode( DataHandling::fetch( $url ) );

			if ( isset( $this->result->meta->error_message ) ) {
				$error = new Exception($this->result->meta->error_message, $this->result->meta->code, null);
				throw $error;
			} else {
				$this->result;
			}

		} catch ( Exception $e ) {
			$this->error = $e;
			//var_dump($this->error);
		}

	}

	protected function get_searchurl( $searchtype, $searchinput ) {

		if ( $searchtype == 'users' && isset($_POST['checktype']) && $_POST['checktype'] == 'users' ) {
			return sprintf( 'https://api.instagram.com/v1/%s/search?q=%s&access_token=%s&count=%s', $searchtype, $searchinput, static::ACCESS_TOKEN, $this->count );
		} else {
			return sprintf( 'https://api.instagram.com/v1/%s/%s/media/recent?access_token=%s&count=%s%s', $searchtype, $searchinput, static::ACCESS_TOKEN, $this->count, $this->pagination );
		}

	}

}
