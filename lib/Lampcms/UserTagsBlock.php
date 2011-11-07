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

class UserTagsBlock extends LampcmsObject
{

	/**
	 * Maximum tags to show in user tags block
	 * A very active user can have hundreds of tags, we
	 * want to show only the 60
	 * most popular tags for this user
	 *
	 *
	 * @var int
	 */
	const MAX_TO_SHOW = 60;


	/**
	 *
	 * Renders block with user tags
	 *
	 * @todo get Viewer from Registry and if NOT
	 * the same as User then get array intersection
	 * and show you have these 'tags' in common
	 *
	 * @param object $Registry Registry object
	 * @param object $User User object
	 */
	public static function get(Registry $Registry, User $User){
		$uid = $User->getUid();
		$aTags = $Registry->Mongo->USER_TAGS->findOne(array('_id' => $uid));

		if(empty($aTags) || empty($aTags['tags'])){
			d('no tags for user: '.$uid);

			return '';
		}

		$aUserTags = $aTags['tags'];
		d('$aUserTags: '.print_r($aUserTags, 1));

		$count = count($aUserTags);

		/**
		 * @todo Translate string
		 */
		$blockTitle = "User's most active tags";
		if($count > self::MAX_TO_SHOW){
			$aUserTags = \array_slice($aUserTags, 0, self::MAX_TO_SHOW);
		}

		$tags = '';
		foreach($aUserTags as $tag => $count){
			$tags .= \tplUserTag::parse(array($tag, $count), false);
		}

		d('tags: '.$tags);

		/**
		 * @todo translate string
		 */
		$vals = array('count' => $blockTitle, 'label' => 'tag', 'tags' => $tags);
		d('vals: '.print_r($vals, 1));

		$ret = \tplUserTags::parse($vals);

		d('ret: '.$ret);

		return $ret;
	}


	/**
	 * @todo finish this
	 *
	 * Finds and parses common tags a Viewer has
	 * with User whos profile user is viewing
	 *
	 * @param User $oViewer
	 * @param array $userTags
	 */
	public static function getCommonTags(User $oViewer, array $userTags){

		$uid = $oViewer->getUid();
		if(0 === $uid){
			return '';
		}

		$aTags = $Registry->Mongo->USER_TAGS->findOne(array('_id' => $uid));

		if(empty($aTags) || empty($aTags['tags'])){
			d('no tags for user: '.$uid);

			return '';
		}

		$viewerTags = $aTags['tags'];

		$aCommon = array_intersect_key($viewerTags, $userTags);
		d('aCommon: '.print_r($aCommon, 1));

		if(empty($aCommon)){
			d('no common tags');

			return '';
		}

	}
}
