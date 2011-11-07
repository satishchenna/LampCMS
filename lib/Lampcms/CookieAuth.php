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

/**
 * Class is responsible
 * for checking the uid and sid cookies
 * if they exist and authenticating
 * the user based on these cookies
 */
class CookieAuth extends UserAuth
{
	/**
	 * Value of 'uid' cookie
	 * @var string
	 */
	protected $uid;

	/**
	 * Value of uid cookie
	 * this is not yet a numeric id, but a raw
	 * string which is in the format of a url query string
	 * @var string
	 */
	protected $cookie;

	/**
	 * Value of sid cookie
	 * @var string
	 */

	protected $sid;

	public function authByCookie(){

		d('$_COOKIE: '.print_r($_COOKIE, 1));

		$User = $this->checkRequiredCookies()
		->validateCookieSalt()
		->checkSidFormat()
		->checkForBannedIP()
		->checkMultipleSidLoginErrors()
		->getSidUser();

		if (!$this->compareSids($User['rs'])) {
			$this->logLoginError($this->uid, $this->sid, true, null, 'cookie');

			throw new CookieAuthException('wrong sid '.print_r($User, 1));
		}

		return $User;
	}


	/**
	 * Get arr of UserInfo from
	 * cache
	 * @return object of type User
	 *
	 * @throws Lampcms\LoginException
	 * in case user does not exist
	 */
	protected function getSidUser(){

		$arrResult = $this->Registry->Mongo->USERS->findOne(array('_id' => $this->uid));

		if (empty($arrResult)) {
			d('user not found with id '.$this->uid);
			$this->logLoginError($this->uid, $this->sid, false, null, 'cookie');

			throw new CookieAuthException('no user by uid cookie');
		}

		return User::factory($this->Registry, $arrResult);
	}


	/**
	 * Validator method
	 * checks to make sure cookies uid and sid
	 * are present in request
	 *
	 * @return object $this
	 *
	 * @throws LampcmsCookieAuthException
	 * if sid or uid cookie are not present
	 * in request
	 */
	protected function checkRequiredCookies(){

		if (!isset($_COOKIE) || empty($_COOKIE['uid']) || empty($_COOKIE['sid'])) {

			throw new CookieAuthException('No uid or sid cookie');
		}

		$this->cookie = \filter_input(INPUT_COOKIE, 'uid', FILTER_SANITIZE_STRING);
		$this->sid = \filter_input(INPUT_COOKIE, 'sid', FILTER_SANITIZE_STRING);

		return $this;
	}


	/**
	 * Parse the value of uid cookie and
	 * it must become an array of uid=>intUserId
	 * and 's'=>saltedUid
	 * then compare the uid+LAMPCMS_COOKIE_SALT hashed
	 * must be equal to 's' value
	 * if any of these steps fails, throw Exception
	 *
	 * @throws \Lampcms\CookieAuthException if cookie string
	 * does not parse or does not validate
	 *
	 * @return object $this
	 */
	protected function validateCookieSalt(){
		parse_str($this->cookie, $a);
		d('parsed cookie: '.print_r($a, 1));
		if(!is_array($a) || !array_key_exists('uid', $a) || !array_key_exists('s', $a)){

			throw new CookieAuthException('Wrong value of uid cookie could not parse it');
		}

		$this->uid = $a['uid'];

		if (!is_numeric($this->uid)) {
			d('invalid uid type: '.gettype($this->uid));
			$this->logLoginError($this->uid, $this->sid, false, null, 'cookie');

			throw new CookieAuthException('"uid" cookie is not numeric: '.$this->uid);
		}

		/**
		 *
		 * Must cast to int because _id of user
		 * in Mongo stored as integer
		 * but must case after checking is_numeric
		 * otherwise it will always be numeric
		 */
		$this->uid = (int)$this->uid;

		$salt = LAMPCMS_COOKIE_SALT;
		d('cookie salt: '.$salt);

		if($a['s'] !== \hash('sha256', $this->uid.$salt)){
			d('salted uid does not match uid');
			$this->logLoginError($this->uid, '', false, null, 'cookie');

			throw new CookieAuthException('"uid" cookie does not match: '.$this->uid);
		}

		return $this;
	}


	/**
	 * Checks that request did not
	 * come from ip address that was previously
	 * banned for hack attempts
	 *
	 * @return object $this
	 *
	 * @throws LampcmsCookieAuthException
	 * if request came from ip address that
	 * was banned for attempting to hack
	 * login by cookie
	 */
	protected function checkForBannedIP(){
		$ip = Request::getIP();

		/**
		 * If any attempt to login by incorrect cookie
		 * came from this ip address in the past 7 days, then
		 * the ip is banned.
		 *
		 * Basically even a single attempt to login by incorrect
		 * cookie will ban the ip address
		 */
		$timediff = (time() - 604800);

		$cur = $this->Registry->Mongo->LOGIN_ERROR
		->find(array('i_ts' => array('$gt' => $timediff)))
		->sort(array('i_ts' => -1));

		foreach($cur as $a){
			if(('cookie' === $a['login_type']) && ($a['ip'] == $ip) ){
				$err = 'Attempted to login by cookie from banned ip address: '.$ip;

				throw new CookieAuthException($err);
			}
		}

		return $this;
	}


	/**
	 * Validator to make sure
	 * 'sid' cookie is a valid hex number
	 *
	 * @return object $this
	 * @throws LampcmsCookieAuthException
	 * if sid cookie is not a valid hex string
	 * string also must be in all lower case letters
	 * in order to pass validation
	 */
	protected function checkSidFormat(){
		if(48 !== $len = \strlen($this->sid)){
			d('invalid sid cookie length: '.$len);
			$this->logLoginError($this->uid, $this->sid, false, null, true);

			throw new CookieAuthException('"sid" cookie is not 48 chars long');
		}


		/**
		 * Check value of sid cookie
		 * it can contain only letters and numbers and a dot
		 * the dot comes from the microtime(true) value
		 * which is always the start of the sid cookie
		 * So basically sid cookie always starts with 10 digits
		 */
		if (preg_match('/[^a-zA-Z0-9\.]+/', $this->sid)) {
			d('invalid sid cookie format: '.$this->sid);
			$this->logLoginError($this->uid, $this->sid, false, null, true);

			throw new CookieAuthException('"sid" cookie is not alphanumeric: '.$this->sid);
		}

		return $this;
	}


	/**
	 * Checks for previous failed auth by cookie
	 * attempts for the same uid
	 * in the last 60 minutes
	 *
	 * @return object $this
	 *
	 * @throws LampcmsCookieAuthException
	 * if there has been any failed attempts
	 * to authenticated the same uid using cookie
	 * in the pas 60 minutes and the latest attempt
	 * was less than 30 minutes ago
	 */
	protected function checkMultipleSidLoginErrors(){
		$now = time();
		$interval = ($now - 3600);
		$wait = 1800;

		$cur = $this->Registry->Mongo->LOGIN_ERROR
		->find(array('usr_lc' => $this->uid, 'i_ts' => array('$gt' => $interval)))
		->sort(array('i_ts' => -1));

		d('$cur: '.gettype($cur).' found count: '.$cur->count());

		$aLockParams = $this->Registry->Ini->getSection('LOGIN_ERROR_LOCK');
		d('$aLockParams: '.print_r($aLockParams, 1));

		if ($cur->count() > (int)$aLockParams['max_errors']) {
			$a1 = $cur->getNext();
			d('a1: '.print_r($a1, 1));
			$lastAttempt = ($now - $a1['i_ts']);

			d('$lastAttempt: '.$lastAttempt);
			if ($lastAttempt < $wait) {
				$remaining = ceil( ($wait - $lastAttempt) / 60);
				/**
				 * @todo
				 * Translate string

				 */
				$strMessage = 'Must wait %d minute%s before trying again';
				$strSuff = ($remaining === 1) ? '' : 's';
				$err = sprintf($strMessage, $remaining, $strSuff);

				throw new CookieAuthException('Multiple login by sid error detected: '.$err);
			}
		}

		d('no multiple login errors detected');

		return $this;
	}


	/**
	 * Compare values of 'sid' cookies
	 * with the value of md5-hashed sid
	 * from the users table
	 *
	 * @param object $stored
	 *
	 * @return bool true if they match
	 * or false if dont match
	 */
	protected function compareSids($stored){

		return ($stored === $this->sid);
	}
}
