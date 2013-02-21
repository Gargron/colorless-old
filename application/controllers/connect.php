<?php

error_reporting(E_ALL);

class Connect extends Controller {

	function Connect()
	{
		parent::Controller();
		$this->load->library('twitter');
	}

	public function index($token = NULL)
	{

		$consumer_key = '';
		$consumer_key_secret = '';

		$tokens['access_token'] = NULL;
		$tokens['access_token_secret'] = NULL;

		// GET THE ACCESS TOKENS

		$oauth_tokens = $this->session->userdata('twitter_oauth_tokens');

		if ( $oauth_tokens !== FALSE ) $tokens = $oauth_tokens;

		if(isset($token)) {
			$token = explode("=", $token);
			$token = $token[1];
		}


		$auth = $this->twitter->oauth($consumer_key, $consumer_key_secret, $tokens['access_token'], $tokens['access_token_secret'], $token);

		if ( isset($auth['access_token']) && isset($auth['access_token_secret']) )
		{
			// SAVE THE ACCESS TOKENS

			$this->session->set_userdata('twitter_oauth_tokens', $auth);

			if ( isset($token) )
			{

				// Now we redirect the user since we've saved their stuff!

				header("Location: /connect");
				return;
			}

		}
		if($oauth_tokens) {
			$res = $this->twitter->call('account/verify_credentials');
			$nee = new stdClass;
			$nee->userName = $res->screen_name;
			$nee->userBlob = new stdClass;
			$nee->userBlob->userAlias = $res->name;
			$nee->userBlob->userHP = $res->url;
			$nee->userBlob->userLocation = $res->location;
			$nee->userBlob->userAbout = $res->description;
			$nee->userBlob->socialTwitter = "http://twitter.com/".$nee->userName;
			$this->session->set_userdata("twitter_data", json_encode($nee));
			print_r($this->session->userdata("twitter_data"));
			redirect("login/register");
		}
	}
}
