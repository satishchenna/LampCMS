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
 *
 * Class for parsing and displaying user's votes stats
 * total votes as well as up/down count
 *
 * @author Dmitri Snytkine
 *
 */
class UserVotesBlock extends LampcmsObject
{


	public static function get(Registry $Registry, User $User){

		$uid = $User->getUid();
		$db = $Registry->Mongo->getDb();

		$map = new \MongoCode("function() { emit(this.t, 1); }");
		$reduce = new \MongoCode("function(k, vals) { ".
    "var sum = 0;".
    "for (var i in vals) {".
        "sum += vals[i];". 
    "}".
    "return sum; }");

		$votes = $db->command(
		array(
			    "mapreduce" => "VOTES", 
			    "map" => $map,
			    "reduce" => $reduce,
			    "query" => array("i_uid" => $uid)
			)
		);

		if(empty($votes) || empty($votes['result'])){
			return '';
		}

		$counter = $db->selectCollection($votes['result'])->find();

		$total = 0;
		$s = '';


		foreach ($counter as $vote) {
			$vars = array('type' => $vote['_id'], 'count' => $vote['value']);
			$total += $vote['value'];
			$s .= \tplVoteCount::parse($vars, false);
		}

		$ret = \tplUserVotes::parse(array('count' => $total, 'stats' => $s, 'username' => $User->getDisplayName()));
		d('ret: '.$ret);

		return $ret;
	}
}
