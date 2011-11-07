<?php
/**
 *
 * License, TERMS and CONDITIONS
 *
 * This software is lisensed under the GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * Please read the license here : http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * ATTRIBUTION REQUIRED
 * 4. All web pages generated by the use of this software, or at least
 * 	  the page that lists the recent questions (usually home page) must include
 *    a link to the http://www.lampcms.com and text of the link must indicate that
 *    the website's Questions/Answers functionality is powered by lampcms.com
 *    An example of acceptable link would be "Powered by <a href="http://www.lampcms.com">LampCMS</a>"
 *    The location of the link is not important, it can be in the footer of the page
 *    but it must not be hidden by style attibutes
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This product includes GeoLite data created by MaxMind,
 *  available from http://www.maxmind.com/
 *
 *
 * @author     Dmitri Snytkine <cms@lampcms.com>
 * @copyright  2005-2011 (or current year) ExamNotes.net inc.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * @link       http://www.lampcms.com   Lampcms.com project
 * @version    Release: @package_version@
 *
 *
 */



namespace Lampcms;

use \Lampcms\Interfaces\FacebookUser;

/**
 * Class for working with Facebook REST Oauth based API
 * @author Dmitri Snytkine
 *
 *
 */
class Facebook extends ExternalAuth
{
	/**
	 * Url of Facebook Graph API for posting message to wall
	 * This is a template url. %s will be replaced with actual facebookID
	 * of User
	 *
	 * This same url can also be used to get data from API, just
	 * set method to GET instead of post
	 * and can get the DATA from Facebook
	 *
	 * @var string
	 */
	protected $wallUpdateUrl = 'https://graph.facebook.com/%s/feed';

	
	/**
	 * Url of facebook API
	 * @var string
	 */
	protected $graphUrl = 'https://graph.facebook.com/me?access_token=';

	
	/**
	 * Object User that we either found
	 * or created new user
	 *
	 * @var object which extends User
	 */
	protected $User;


	/**
	 * access token we get from fb cookie
	 *
	 * @var string
	 */
	protected $sAccessToken = null;

	
	/**
	 * Facebook Application ID
	 * You get this by setting up your own application on facebook
	 * here: http://www.facebook.com/developers/
	 *
	 * @var string
	 */
	protected $sAppId = null;


	/**
	 * Object returned by \Lampcms\Curl
	 *
	 * @var object
	 */
	protected $oResponse;

	
	/**
	 * Object of type \Lampcms\Curl;
	 * @var object
	 */
	protected $oHTTP;


	/**
	 * Array of data returned from Facebook server
	 * @var array
	 */
	protected $aFbUserData = array();


	/**
	 * Constructor
	 * 
	 * @param object $Registry
	 * @param object $User
	 */
	protected function __construct(Registry $Registry, FacebookUser $User = null){
		parent::__construct($Registry);
		$this->User = (null !== $User) ? $User : $Registry->Viewer;
		$this->initHttpObject();
	}


	/**
	 * Instantiates (or resets) the
	 * $this->oHTTP which is our instance of Curl class
	 */
	protected function initHttpObject(){
		$this->oHTTP = new Curl();

		return $this;
	}


	/**
	 * Setter for $this->User
	 *
	 * @param FacebookUser $User
	 * @return object $this
	 */
	public function setUser(FacebookUser $User){
		$this->User = $User;

		return $this;
	}


	/**
	 * Post message to the wall of user
	 *
	 * @param array $aData must include at least 'message' key
	 * with actual message to post some html allowed, some not.
	 * It's up to facebook to decide which html is not allowed
	 *
	 * @param object $Registry Registry object
	 * @param object $User user object or null
	 * in case of null the currently logged in user is used
	 *
	 * @return mixed whatever is returned by postUpdate method
	 * @see postUpdate()
	 */
	public static function postToWall(Registry $Registry, $aData, FacebookUser $User = null){
		$o = new self($Registry, $User);
		return $o->postUpdate($aData);
	}


	/**
	 * Post update to user Wall
	 *
	 *
	 * @param mixed array $aData | string can provide just
	 * a string it will be posted to Facebook User's Wall as a message
	 * it can contain some html code - it's up to Facebook to allow
	 * or disallow certain html tags
	 *
	 * @return mixed if successful post to Facebook API
	 * then it will return the string returned by API
	 * This could be raw string of json data - not json decoded yet
	 * or false in case there were some errors
	 *
	 * @throws FacebookApiException in case of errors with
	 * using API or more general \Lampcms\Exception in case there
	 * were some other problems sowhere along the line like
	 * in case with Curl object
	 *
	 */
	public function postUpdate($aData){

		if(!is_string($aData) && !is_array($aData)){
			throw new \InvalidArgumentException('Invalid data type of $aData: '.\gettype($aData));
		}

		$aData = \is_array($aData) ? $aData : array('message' => $aData);

		$facebookUid = $this->User->getFacebookUid();
		$facebookToken = $this->User->getFacebookToken();
		d('$facebookUid: '.$facebookUid.' $facebookToken: '.$facebookToken);

		if(empty($facebookUid) || empty($facebookToken)){
			d('User is not connected with Facebook');

			return false;
		}

		$aData['access_token'] = $this->User->getFacebookToken();
		d('$aData: '.print_r($aData, 1));

		$url = \sprintf($this->wallUpdateUrl, $facebookUid);
		d('cp url: '.$url);;
		try{
			$this->oHTTP->getDocument($url, null, null, array('formVars' => $aData))->checkResponse();
			$retCode = $this->oHTTP->getHttpResponseCode();
			$body = $this->oHTTP->getResponseBody();
			d('retCode: '.$retCode.' resp: '.$body);
			return $body;
		} catch(HttpTimeoutException $e ){
			d('Request to Facebook server timedout');
			throw new FacebookApiException('Request to Facebook server timed out. Please try again later');
		} catch(Http401Exception $e){
			d('Unauthorized to get data from Facebook, most likely user unjoined the site');
			$this->revokeFacebookConnect();
			throw new FacebookApiException('Anauthorized with Facebook');
		} catch(HttpResponseCodeException $e){
			e('LampcmsError Facebook response exception: '.$e->getHttpCode().' '.$e->getMessage().' body: '.$this->oHTTP->getResponseBody());
			/**
			 * The non-200 response code means there is some kind
			 * of error, maybe authorization failed or something like that,
			 * or maybe Facebook server was acting up,
			 * in this case it is better to delete cookies
			 * so that we dont go through these steps again.
			 * User will just have to re-do the login fir GFC step
			 */

			throw new FacebookApiException('Error during authentication with Facebook server');
		}catch (\Exception $e){
			e('Unable to post: '.$e->getMessage().' code: '.$e->getCode());
			throw $e;
		}

		d('cp');

		return false;
	}


	/**
	 * Validation to make sure data array
	 * has required keys 'message'
	 * @param unknown_type $aData
	 */
	protected function validateData(array &$aData){
		if(empty($aData['message'])){
			throw new FacebookApiException('Array of data must contain key "message" and its value cannot be empty');
		}

		if(empty($aData['access_token'])){
			$aData['access_token'] = $this->User->getFacebookToken();
		}
	}


	/**
	 * In case we got the 401 Error this means
	 * user is no longer authorizing us to post
	 * to wall
	 *
	 * What do we do?
	 * We can delete from USERS_FACEBOOK
	 * or just mark it or just remove the access token
	 *
	 * But what if user only remove one permission like
	 * to post updates or to post while offline?
	 *
	 * but still authorizes us to do stuff like
	 * get email address?
	 *
	 *@todo finish this
	 */
	protected function revokeFacebookConnect(){
		/**
		 * Why uid is 0?
		 * This means user viewer is not logged in, but why?
		 *
		 */
		d('$this->User: '.get_class($this->User).' '.print_r($this->User->getArrayCopy(), 1));

		$this->User->revokeFacebookConnect();

		return $this;
	}
}
