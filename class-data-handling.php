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

class InstagramUsers {

	public $result;
	public $error = false;
	private $access_token = '414143281.e2a9043.6d4acb839c38488f831d826bf29d32fe';


	public function __construct( $count = 4 ) {

		$this->count = $count;

		try {

			$this->result = json_decode( DataHandling::fetch( 'https://api.instagram.com/v1/users/search?q=' . $_POST['user_id'] . '&access_token=' . $this->access_token . '&count=' . $this->count ) );

			if ( isset( $this->result->meta->error_message ) ) {
				$this->error = $this->result->meta->error_message;
			} else {
				$this->result;
			}

		} catch ( Exception $e ) {
			$this->error = $e->getMessage();
		}

	}

}

class InstagramPosts {

	public $result;
	public $error = false;

	private $access_token = '414143281.e2a9043.6d4acb839c38488f831d826bf29d32fe';


	public function __construct( $count = 30 ) {

		$this->count      = $count;
		$this->pagination = isset( $_POST['next_max_id'] ) ? '&max_id=' . $_POST['next_max_id'] : '';

		try {

			$this->result = json_decode( DataHandling::fetch( 'https://api.instagram.com/v1/users/' . $_POST['chosen_user'] . '/media/recent?access_token=' . $this->access_token . '&count=' . $this->count . $this->pagination ) );

			if ( isset( $this->result->meta->error_message ) ) {
				$this->error = $this->result->meta->error_message;
			} else {

				$this->result;

			}

		} catch ( Exception $e ) {
			$this->error = $e->getMessage();
		}

	}

}