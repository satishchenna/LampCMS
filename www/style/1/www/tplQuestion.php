<?php
/**
 *
 * PHP 5.3 or better is required
 *
 * @package    Global functions
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
 * 4. All web pages generated by the use of this software must include
 *    a link to the www.lampcms.com and text of the link must indicate that
 *    the website's Questions/Answers functionality is powered by lampcms.com
 *    An example of acceptable link would be "Powered by <a href="http://www.lampcms.com">lampcms</a>"
 *    The location of the link is not important, it can be in the footer of the page
 *    but it must not be hidden by style attibutes and the link tag must NOT
 *    have the rel="nofollow" attribute
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
 *
 * @author     Dmitri Snytkine <cms@lampcms.com>
 * @copyright  2005-2011 (or current year) ExamNotes.net inc.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt The GNU General Public License (GPL) version 3
 * @link       http://cms.lampcms.com   Lampcms.com project
 * @version    Release: @package_version@
 *
 *
 */

/**
 * Template to render div with one question
 * It will be complete with
 * up/down vote data and links
 * IsAcceptedAnswer check mark,
 *
 * Question body, tags, author block
 * datetime
 *
 *  IMPORTANT: this template will accept array
 *  straight from QUESTIONS collection
 *
 * @author Dmitri Snytkine
 *
 */
class tplQuestion extends Lampcms\Template\Fast
{
	
	protected static function func(&$a){
		if(array_key_exists('a_edited', $a)){
			/**
			 * A way to pass "translated"
			 * version of "Edited" word
			 * to the tplEditedby template
			 */
			$aEdited = end($a['a_edited']);
			$aEdited['edited'] = $a['edited'];
			
			$a['edits'] = \tplEditedby::parse($aEdited, false);
		}
		
		if(!empty($a['i_sticky'])){
			$a['sticky'] = ' sticky';
		}
		
		if(!empty($a['a_comments'])){
			/**
			 * Closure function
			 * to pass resource_id 
			 * and author id of this
			 * Question to the tplComments
			 * This way we don't have to store
			 * duplicate data in each comment
			 * element and still be able to
			 * have access to these 2 important
			 * fields in the tplComments template
			 * We going to need id or resource owner
			 * in order to add it to the "reply" link
			 * in the form of class uid-$uid
			 * 
			 */
			$rid     = $a['_id'];
			$uid     = $a['i_uid'];
			$reply   = $a['reply'];
			$reply_t = $a['reply_t'];
			
			$f = function(&$data) use ($rid, $uid, $reply, $reply_t){
				$data['resource_id'] = $rid;
				$data['owner_id']    = $uid;
				$data['reply']       = $reply;
				$data['reply_t']     = $reply_t;
			};
			
			$a['comments_html'] = tplComment::loop($a['a_comments'], true, $f); //
		}
	}
	
	protected static $vars = array(
	'_id' => '', // 1
	'b' => '', // 2
	'ulink' => '', // 3
	'avtr' => '', // 4
	'tags_html' => '', // 5
	'credits' => '', // 6
	'hts' => '', // 7
	'i_votes' => '', // 8
	'i_favs' => '', // 9
	'i_uid' => '0', // 10 Question author id
	'i_views' => '0', // 11
	'vw_s' => 's',  // 12
	'vote_up' => "\xE2\x87\xA7", // 13 \xE2\x87\xA7
	'vote_down' => "\xE2\x87\xA9", // 14
	'i_flags' => '', //15
	'deleted' => '', // 16
	'deletedby' => '', //17
	'edits' => '', //18
	'sticky' => '', //19
	'comments_html' => '', //20
	'i_comments' => '0', //21
	'nocomments' => '', //22
	'add_comment' => 'add comment' //23
	);

	protected static $tpl = '
	<table class="question_table%16$s">
	<tr>
		<td class="td_votes" align="center" width="90px">
		<div class="votebtns" id="vote%1$s">
		<a id="upvote-%1$s"
			title="I like this post (click again to cancel)"
			class="ajax ttt vote thumbup" href="/vote/%1$s/up" rel="nofollow">%13$s</a>
		<div id="score%1$s" class="qscore">%8$s</div>

		<a id="downvote-%1$s"
			title="I dont like this post (click again to cancel)"
			class="ajax ttt vote thumbdown" href="/vote/%1$s/down" rel="nofollow">%14$s</a> 
		<br>
			<!--
			<a id="favorite-%1$s"
			title="mark/unmark this question as favorite (click again to cancel)"
			class="ajax ttt favorite-mark" href="/mark_favorite/%1$s" rel="nofollow">favorite</a>

			<div id="fvrt-count">%9$s</div>
			//-->
		   <div class="vws" title="%11$s view%12$s">%11$s <span rel="in">view%12$s</span></div>
		</div>
		<div id="share">
		<div id="meme">
				<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
				<a href="http://twitter.com/share" class="twitter-share-button"
				data-via="devcomments" data-related="snytkine:For Great tips for php and JavaScript developers"
				data-count="vertical">Tweet</a>
		</div>
		<div id="fbshare"><fb:like layout="button_count" width="120" font="verdana"></fb:like></div>
		</div>
		</td>

		<td class="td_question">
		<div class="question-body" id="qbody-%1$s">%2$s</div>
		<div class="fl cb tgs">%5$s</div>
		<div class="question%19$s controls%16$s uid-%10$s cb" id="res_%1$s">
		     <span class="icoc stub fr">&nbsp;</span><span class="ico ttt flag ajax" title="Flag this question as inappropriate"> </span>
		</div>
		<!-- // -->
		<table class="foot">
          <tr>
            <td class="edits">
                %18$s
            </td>
            <td class="td_poster owner">
            <div class="usr_info">
            <div class="qtime">asked <span title="%7$s" class="ts">%7$s</span></div>
            <div class="avtr32">
             <img src="%4$s" height="32" width="32" alt="">
            </div>
            	<div class="usr_details">
            	%3$s<br>
            	<span class="reputation" title="reputation score"></span>
				</div>
			</div>
            </td>
          </tr>
        </table>
        %6$s
        %17$s
		</td>
	</tr>
	<tr>
	<td></td>
	<td>
		<div class="comments%22$s i_comments_%21$s" id="comments-%1$s">
			%20$s
			<div class="add_com cb fl">
				<span class="ico comment fl"> </span><a href="#" class="ajax com_link uid-%10$s" id="comlink_%1$s">%23$s</a>
			</div>
		</div>
	</td>
	</tr>
	</table>';
}
