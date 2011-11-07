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
 *    the website\'s Questions/Answers functionality is powered by lampcms.com
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


namespace Lampcms\Api;


/**
 * Class represents a registered API client
 * API client has unique client ID and is allowed to access
 * OUR API
 *
 * @author Dmitri Snytkine
 *
 */
class Clientdata extends \Lampcms\Mongo\Doc
{

	protected $aReturn = array('app', 'api_key');

	public function __construct(\Lampcms\Registry $Registry){

		parent::__construct($Registry,  'API_CLIENTS');
	}

	/**
	 * Factory method
	 * @todo when support for OAuth2 is added
	 * then we will check if OAuth2 is enable - return
	 * OAuth2 sub-class.
	 * It will have extra methods
	 * to return and save extra data
	 *
	 * @param Registry $Registry
	 */
	public static function factory(\Lampcms\Registry $Registry){
		return new static($Registry);
	}


	/**
	 * Get SRC if the icon image
	 * Will return default path
	 * to a generic app icon
	 * if this client has not uploaded
	 * own icon.
	 *
	 * @return string value of the $src of the image
	 */
	public function getIcon($asHtml = true){
		$s = $this->offsetGet('icon');

		$s = (empty($s)) ? '/images/app2.png' : LAMPCMS_AVATAR_IMG_SITE.\Lampcms\PATH_WWW_IMG_AVATAR_SQUARE.$s;

		if(!$asHtml){
			return $s;
		}
		
		return '<img src="'.$s.'" width="72px" height="72px" alt="App Logo">';
	}


	/**
	 * Return array of key->value
	 * only return certain keys that should
	 * be displayed on the Cliend info page
	 * This includes api_key, name
	 * In case of OAuth2 sub-class show
	 * app token as well as urls for end-points
	 * related to OAuth2 authorization and
	 * other important urls
	 *
	 * @return array
	 */
	public function getData(){
		$ret = array(
		'Application' => $this->offsetGet('app_name'),
		'About' => '<span class="pre">'.$this->offsetGet('about').'</span>',
		'Icon' => $this->getIcon(true),
		'API Key' => $this->offsetGet('api_key'));

		return $ret;
	}
}
