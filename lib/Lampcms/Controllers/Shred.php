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


namespace Lampcms\Controllers;

use \Lampcms\WebPage;
use \Lampcms\Request;
use \Lampcms\Responder;
use \Lampcms\Answer;

class Shred extends WebPage
{
	protected $bRequirePost = true;

	protected $requireToken = true;

	protected $permission = 'shred_user';

	protected $aRequired = array('uid');

	protected $aIPs = array();

	protected $aCountries = array();

	/**
	 * Array of question IDs affected
	 * if answers are deleted
	 *
	 * @var array
	 */
	protected $aQuestions = array();


	protected $Cache;


	protected function main(){
		/**
		 * Need to instantiate Cache so that it
		 * will listen to event and unset some keys
		 */
		$this->Cache = $this->Registry->Cache;
		$this->excludeAdmin()
		->deleteQuestions()
		->deleteAnswers()
		->deleteUserTags()
		->banIPs()
		->deleteUser()
		->postEvent()
		->returnResult();
	}


	/**
	 * Make sure we don't accidentely delete
	 * administrator user
	 *
	 * @return object $this
	 */
	protected function excludeAdmin(){

		$aUser = $this->Registry->Mongo->USERS->findOne(array('_id' => $this->Request['uid']));
		if($aUser && ('administrator' === $aUser['role'])){
			throw new \Lampcms\Exception('Dude! Not cool! You cannot shred the admin user');
		}

		return $this;
	}


	/**
	 * This is important as it will cause
	 * the removal cached value of 'recent questions'
	 * and 'unanswered' tags
	 *
	 * @return object $this
	 */
	protected function postEvent(){
		$this->Registry->Dispatcher->post($this, 'onResourceDelete');

		return $this;
	}


	/**
	 * Delete all questions by this user
	 * as well as remove tags
	 * and add ip address of every question
	 * as key to $this->aIPs array
	 * (as keys and not as values so that
	 * only unique values are added)
	 *
	 * If question is unanswered, also remove from
	 * UNANSWERED_TAGS collection
	 *
	 * @return object $this
	 */
	protected function deleteQuestions(){
		$coll = $this->Registry->Mongo->QUESTIONS;
		$uid = (int)$this->Request['uid'];
		$cur = $coll->find(array('i_uid' => $uid));
		if($cur && $cur->count() > 0){
			d('got '.$cur->count().' questions to delete');
			foreach($cur as $a){

				if(!empty($a['ip'])){
					$this->aIPs[$a['ip']] = 1;
				}

				/**
				 * If this question is unanswered then also
				 * remove from UNANSWERED_TAGS
				 */
				if(empty($a['i_sel_ans']) && !empty($a['a_tags']) ){
					d('going to add to Unanswered tags');
					\Lampcms\UnansweredTags::factory($this->Registry)->remove($a['a_tags']);
				}

				/**
				 * Remove from QUESTION_TAGS
				 */
				if(!empty($a['a_tags'])){
					\Lampcms\Qtagscounter::factory($this->Registry)->removeTags($a['a_tags']);
				}

			}

			/**
			 * Now delete actual question
			 */
			$res = $coll->remove(array('i_uid' => $uid), array('safe' => true));
			d('questions removed: '.print_r($res, 1));
		}

		return $this;
	}


	/**
	 * Delete all answers made by user
	 * Also update questions affected by
	 * deletion of those answers
	 *
	 * @return object $this
	 */
	protected function deleteAnswers(){
		$coll = $this->Registry->Mongo->ANSWERS;
		$cur = $coll->find(array('i_uid' => $this->Request['uid']));
		if($cur && ($cur->count() > 0)){

			foreach($cur as $a){
				
				$Question = new \Lampcms\Question($this->Registry);
				try{
					$Question->by_id((int)$Answer->getQuestionId());
					$Answer = new Answer($this->Registry, $a);
					$Question->removeAnswer($Answer);
					$Question->save();
					/**
					 * setSaved() because we don't need auto-save feature 
					 * to save each answer 
					 * since all answers will be removed at end of this method
					 */
					$Answer->setSaved(); 
				} catch(\MongoException $e){
					d('Question not found by _id: '.$a['i_qid']);
				}
	
				if(!empty($a['cc'])){
					$this->aCountries[] = $a['cc'];
				}
			}

			$res = $coll->remove(array('i_uid' => $this->Request['uid']), array('safe' => true));
			d('questions removed: '.print_r($res, 1));
		}

		return $this;
	}



	protected function deleteUserTags(){
		$coll = $this->Registry->Mongo->USER_TAGS;
		$res = $coll->remove(array('_id' => $this->Request['uid']), array('safe' => true));
		d('questions removed: '.print_r($res, 1));

		return $this;
	}


	/**
	 * Change role of user to 'deleted'
	 * This is better than actually deleting the user
	 * since it's still possible to 'undelete' user
	 * if necessary and also it makes it more difficult
	 * for this user to just re-register beause
	 * user will not be able to reuse email address
	 *
	 * @return object $this
	 */
	protected function deleteUser(){

		$this->Registry->Mongo->USERS->update(array('_id' => $this->Request['uid']),
		array('$set' => array('role' => 'deleted')),
		array('safe' => true));

		return $this;
	}


	/**
	 * Add IPs from user posted to the
	 * BANNED_IP collection
	 *
	 * @return object $this
	 */
	protected function banIPs(){
		d('aIPs: '.print_r($this->aIPs, 1));
		if(!empty($this->aIPs)){
			$IPS = array_keys($this->aIPs);
			foreach($IPS as $val){
				d('banning IP '.$val);
				try{
					$this->Registry->Mongo->BANNED_IP->insert(array('_id' => $val), array('safe' => true));
				} catch (\MongoException $e){
					d('IP address '.$val.' already banned');
				}
			}
		}

		return $this;
	}



	protected function returnResult(){

		if(Request::isAjax()){

			$message = 'User Shredded<hr>Banned IPs:'.implode('<br>', array_keys($this->aIPs)).
			'<hr><br>Countries: '.implode('<br>', array_keys($this->aCountries));

			Responder::sendJSON(array('alert' => $message));
		}

		Responder::redirectToPage();

	}

}
