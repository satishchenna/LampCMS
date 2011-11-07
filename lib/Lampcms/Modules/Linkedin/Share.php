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


namespace Lampcms\Modules\Linkedin;


/**
 * Class for posting Questions ans Answers
 * to LinkedIn as status updates (share) via Oauth API
 *
 *
 * @author Dmitri Snytkine
 *
 */
class Share extends \Lampcms\Event\Observer
{
	/**
	 * LINKEDIN API Config array
	 * a LINKEDIN section from !config.ini
	 *
	 * @var array
	 */
	protected $aConfig;

	public function main(){

		$a = $this->Registry->Request->getArray();
		if(empty($a['linkedin'])){
			d('linkedin checkbox not checked');
			/**
			 * Set the preference in Viewer object
			 * for that "Post to linkedin" checkbox to be not checked
			 * This is just in case it was checked before
			 */
			$this->Registry->Viewer['b_li'] = false;

			return;
		}

		/**
		 * First if this site does not have support for linkedin API
		 * OR if User does not have linkedin credentials then
		 * there is nothing to do here
		 * This is unlikely because user without linkedin credentials
		 * will not get to see the checkbox to post to linkedin
		 * but still it's better to check just to be sure
		 */
		if(!extension_loaded('curl')){
			d('curl extension not present, exiting');

			return;
		}

		try{

			$this->aConfig = $this->Registry->Ini->getSection('LINKEDIN');

			if(empty($this->aConfig)
			|| empty($this->aConfig['OAUTH_KEY'])
			|| empty($this->aConfig['OAUTH_SECRET'])){
				d('linkedin API not enabled on this site');

				return;;
			}
		} catch (\Lampcms\IniException $e){
			d('Ini Exception: '.$e->getMessage());

			return;
		}

		if(null === $this->Registry->Viewer->getLinkedinToken()){
			d('User does not have linkedin token');
			return;
		}

		/**
		 * Now we know that user checked that checkbox
		 * to post content to linkedin
		 * and we now going to save this preference
		 * in User object
		 *
		 */
		$this->Registry->Viewer['b_li'] = true;
		d('cp');
		switch($this->eventName){
			case 'onNewQuestion':
			case 'onNewAnswer':
				$this->post();
				break;
		}
	}


	protected function post(){
		$url = $this->obj->getUrl();
		$label = $this->obj->getTitle();
		$comment = 'I asked this on '.$this->Registry->Ini->SITE_NAME;

		if($this->obj instanceof \Lampcms\Answer){
			$comment = 'My answer to "'.$label.'"';
		}
		
		d('$comment: '.$comment.' label: '.$label.' $url: '.$url);

		$reward = \Lampcms\Points::SHARED_CONTENT;
		$User = $this->Registry->Viewer;

		$token  = $User->getLinkedinToken();
		$secret = $User->getlinkedinSecret();

		try{
			$oLI = new ApiClient($this->aConfig['OAUTH_KEY'], $this->aConfig['OAUTH_SECRET']);
			$oLI->setUserToken($token, $secret);
		} catch (\Exception $e){
			e('Error during setup of Linkedin ApiClient object '.$e->getMessage().' in '.$e->getFile().' on '.$e->getLine());

			return $this;
		}

		$func = function() use ($oLI, $User, $comment, $label, $url, $reward){
			try{
				$oLI->share($comment, $label, $url);
			} catch (\Exception $e){
				return;
			}

			$User->setReputation($reward);
		};

		\Lampcms\runLater($func);

	}

}
