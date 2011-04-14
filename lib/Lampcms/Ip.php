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

/*
 XIP Class - Proxy Detection and IP Functions class for PHP
 Copyright (C) 2004-2006 Volkan Kьзьkзakar. All Rights Reserved.
 (Volkan Kucukcakar)
 http://www.developera.com
 You are requested to retain this copyright notice in order to use
 this software.
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the  Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */


/*
 *** Please do not remove this information text and comments ***
 *
 *   Copyright (C) 2004-2006 Volkan Kьзьkзakar.
 *   (Volkan Kucukcakar)
 *
 * Name          : XIP Class
 * Version       : 0.3.00
 * Date          : 2006.08.21
 * File          : class.XIP.php
 * Author        : Volkan Kьзьkзakar
 *                (Volkan Kucukcakar)
 * EMail         : volkank@developera.com
 * Home Page     : http://www.developera.com
 * Description   : XIP Class
 *
 *           ***** Proxy Detection and IP Functions class for PHP
 *
 *                 Features:
 *                 =========
 *
 *                 -Very easy to integrate and use
 *                 -Enhanced smart "Proxy" and "Client IP" detection using header analysis
 *                 -Detects Proxy by looking for more than 40 standard and non-standard headers and hostname
 *                 -Retrieves the real Client IP address !!!
 *                 -IP BLACKLIST, WHITELIST check !!!
 *                 -IP[/Mask] format (IP range) compatible
 *                 -IP validate
 *                 -IP public/private check
 *                 -Expandable proxy detection structure by using arrays and regular expressions
 *                 -Guess for unknown  headers using regular expressions
 *
 *
 *
 * History
 * =======
 *
 * v0.1.0 (2004)           : Foundation. "Proxy","Client IP","Proxy Type" detection.
 * v0.2.0 (2005)           : -Enhanced smart Header analysis techniques
 *                           -Expandable structure by using arrays
 *                           -Regular expression compatible
 *                           -IP Validation Function added
 *                           -Added function to check if IP is local
 *                           -Invalid and Local IP adresses are ignored if reported as client ip
 * v0.2.1 (2005)           : -search REMOTE_HOST (for words "proxy", "cache")
 * v0.2.23(2006.02.16)     : First and public release
 *                           -Well commented
 * v0.2.24(2006.03.12)     : -Fixed some notice level error reporting
 *                           -Fixed Normal Private IP List
 *                           -Added some comments
 * v0.2.30(2006.04.25)     : -Fixed a bug in NetCheck() function relevant to a undefined variable
 *                           -NetCheck() function is deprecated, parameter order changed in new CheckNet() function
 *                           -More easily blacklist check with new CheckNet() function
 *                           ($ip parameter became optional and refers to client IP by default)
 *                           -Fixed a potential bug (Added IP Validation check) in isPublic() function
 *                           -Changed some comments and explanations
 * v0.2.40(2006.07.05)     : -Fixed IP octet pattern
 *                           -Fixed Normal Private IP List
 *                           -Added some security advice
 *                           -Added/Changed some comments and explanations
 * v0.2.41(2006.07.07)     : -Added/Changed some comments and explanations
 * v0.3.00(2006.08.21)     : -Bug Fixed in IP octet pattern
 *                           -Added Example 3: Blacklist (Local) - Another local blacklist example using an external flat text file
 *                           -Added Example 4: IP Log  - IP Log example using a flat text file (with exclusive file lock support via flock and semaphore files)
 *                           -Added Example 5: Blacklist (Check RBL) - Checking the existance of visitor's IP in RBL (Real-Time Blackhole List) using 3rd party services
 *
 * Nov 16, 2007      Dmitri Snytkine added function
 * fnExtractIp to extract ip address from email "received" header
 *** Please do not remove this information text and comments ***
 */

/////////////////////////////////////////////////////////////////////
// XIP CLASS
/////////////////////////////////////////////////////////////////////

class Ip
{

	/////////////////////////////////////////////////////////////////////
	// VARIABLES
	/////////////////////////////////////////////////////////////////////

	const XIP_HT_None = 1;

	const XIP_HT_PName = 2;

	const XIP_HT_PInfo = 3;

	const XIP_HT_ClientIP = 4;


	/**
	 *   Info about IP, Automatically filled for user on object creation
	 *        $XIP->IP['proxy'] =  string, Proxy IP (just ordinary REMOTE_ADDR value)
	 *        $XIP->IP['client'] = string, Real Client IP reported in HTTP headers; will return Proxy IP on Anonymous proxies
	 *        $XIP->IP['all'] =  string, All IP addressed seperated by comma (REMOTE_ADDR+other reported addresses)
	 *
	 *        Note: You can usually use $XIP->IP['client'] instead of $_SERVER['REMOTE_ADDR'] or $REMOTE_ADDR in your project however;
	 *
	 *              * Do not forget that client IP is always reported by gateway (or client unfortinately)
	 *                By the way, this is a property of http proxy connection; not a weakness of XIP Class.
	 *              * You can separately use/save $_SERVER['REMOTE_ADDR'] (or $XIP->IP['proxy']) in your logs for security. (See EXAMPLE 4 - iplog.php)
	 *              * To increase security, you can use $XIP->IP['client'] in conjunction with $XIP->IP['proxy'] (equals to $_SERVER['REMOTE_ADDR']),
	 *                or you can do something with $XIP->IP['all']
	 *
	 */

	var $IP = array();


	/**
	 *   Info about Proxy, Automatically filled for user on object creation
	 *        $XIP->Proxy['detected'] = boolean, true if proxy is detected
	 *        $XIP->Proxy['suspicious'] = boolean, true if proxy detection is suspicious
	 *        $XIP->Proxy['anonymous'] = boolean, true if proxy is anonymous, false if proxy is transparent
	 *        $XIP->Proxy['name'] = string, proxy server name
	 *        $XIP->Proxy['info'] = array, other info
	 *        $XIP->Proxy['headers'] = array, raw data and headers containing proxy evidence
	 */

	var $Proxy = array();

	/**
	 *   Decide using Client IP or Proxy IP if $ip partameter omitted in CheckNet() function.
	 *   By default, $BL_Safe=false and class uses the detected Client IP in CheckNet().
	 *   Client IP ($XIP->IP['client']) is reported by the requesting client or proxy; so it is cheatable theoretically.
	 *   If you use CheckNet() function as an IP BLACKLIST check; you may want to use Proxy IP ($XIP->IP['proxy']) by default
	 *   (Also it is always equal to $_SERVER['REMOTE_ADDR'] or $REMOTE_ADDR)
	 *
	 *   Note that if you make $BL_Safe=true; you can only blacklist a whole proxy instead of blacklisting one client.
	 */

	var $BL_Safe = false;


	/**
	 *   Normal Private IP List, to detect if IP is local (and to ignore if reported by proxy)
	 *   This is a simple and fixed list, I have compiled according to RFC 3330 (and some other resource)
	 *   http://www.rfc-archive.org/getrfc.php?rfc=3330
	 */

	var $Private_IP_Normal = '0.0.0.0/8, 1.0.0.0/8, 2.0.0.0/8, 10.0.0.0/8,
                              127.0.0.0/8, 169.254.0.0/16, 172.16.0.0/12, 192.0.2.0/24,
                              192.168.0.0/16, 198.18.0.0/15, 224.0.0.0/3';
	/*
	 0.0.0.0/8, "This" Network
	 10.0.0.0/8, Private-Use Networks
	 127.0.0.0/8, Loopback
	 169.254.0.0/16, Link Local
	 172.16.0.0/12, Private-Use Networks
	 192.0.2.0/24, Test-Net
	 192.168.0.0/16, Private-Use Networks
	 198.18.0.0/15, Network Interconnect Device Benchmark Testing
	 */

	/**
	 *   Extended Private IP List, to detect if IP is local (and to ignore if reported by proxy)
	 *   This is a more extended BOGON IP list, which can change in time according to reservations / allocations by IANA
	 *   Last updated 2006.01.05
	 *   http://www.cymru.com/Documents/bogon-list.html - Bogon List 3.1 - 05 JAN 2006
	 *   BTW you can always download the latest version of list into a file from http://www.cymru.com/Documents/bogon-bn-nonagg.txt
	 *   and specify the local file name to variable $exfile below.
	 *   Thanks to "Team Cymru Web Site"
	 */

	var $Private_IP_Extended = '0.0.0.0/8, 1.0.0.0/8, 2.0.0.0/8, 5.0.0.0/8,
                                 7.0.0.0/8, 10.0.0.0/8, 23.0.0.0/8, 27.0.0.0/8,
                                 31.0.0.0/8, 36.0.0.0/8, 37.0.0.0/8, 39.0.0.0/8,
                                 42.0.0.0/8, 49.0.0.0/8, 50.0.0.0/8, 77.0.0.0/8,
                                 78.0.0.0/8, 79.0.0.0/8, 92.0.0.0/8, 93.0.0.0/8,
                                 94.0.0.0/8, 95.0.0.0/8, 96.0.0.0/8, 97.0.0.0/8,
                                 98.0.0.0/8, 99.0.0.0/8, 100.0.0.0/8, 101.0.0.0/8,
                                 102.0.0.0/8, 103.0.0.0/8, 104.0.0.0/8, 105.0.0.0/8,
                                 106.0.0.0/8, 107.0.0.0/8, 108.0.0.0/8, 109.0.0.0/8,
                                 110.0.0.0/8, 111.0.0.0/8, 112.0.0.0/8, 113.0.0.0/8,
                                 114.0.0.0/8, 115.0.0.0/8, 116.0.0.0/8, 117.0.0.0/8,
                                 118.0.0.0/8, 119.0.0.0/8, 120.0.0.0/8, 127.0.0.0/8,
                                 169.254.0.0/16, 172.16.0.0/12, 173.0.0.0/8, 174.0.0.0/8,
                                 175.0.0.0/8, 176.0.0.0/8, 177.0.0.0/8, 178.0.0.0/8,
                                 179.0.0.0/8, 180.0.0.0/8, 181.0.0.0/8, 182.0.0.0/8,
                                 183.0.0.0/8, 184.0.0.0/8, 185.0.0.0/8, 186.0.0.0/8,
                                 187.0.0.0/8, 192.0.2.0/24, 192.168.0.0/16, 197.0.0.0/8,
                                 198.18.0.0/15, 223.0.0.0/8, 224.0.0.0/3';

	var $exfile = ''; //Load Extended Private IP Address List from this file (will overwrite existing list)

	var $ex_private = false; //Use Extended List in private IP detection

	var $ex_proxy = false; //Use Extended List in proxy (client IP) detection


	/**
	 *   INTERNAL USE - Proxy Evidence Headers
	 *   $Proxy_Evidence array is an EXPANDABLE STRUCTURE,
	 *   which is EVALUATED on headers to make decisions
	 *   on proxy certainty, client IP, proxy name, other info
	 *
	 *   These decision headers are made according to my small research about proxy
	 *   behaviors, proxies do not always behave like RFC's say, so there can be a big
	 *   disorder of standard and non standard headers and behaviors.
	 */

	private $Proxy_Evidence = array(        /**
	*   [0]=string HeaderName,[1]=constant HeaderType,[2]=boolean ProxyCertainty, ['value']=string Value
	*
	*   HeaderName    : Header name as string or regular expression
	*   HeaderType    : What kind of info can header contain ?
	*                   (see CONSTANTS section above for explanations...)
	*   ProxyCertainty: Is proxy certainly present if header found?
	*   Value         : Optional parameter must be regular expression,
	*                   Header is only accepted if matches value (regular expression)
	*
	*   Note that headers are written importance ordered, first written header is evaluated first
	*/
	array('HTTP_VIA', self::XIP_HT_PName, true),
	// example.com:3128 (Squid/2.4.STABLE6)
	array('HTTP_PROXY_CONNECTION', self::XIP_HT_None, true),
	//Keep-Alive
	array('HTTP_XROXY_CONNECTION', self::XIP_HT_None, true),
	//Keep-Alive
	array('HTTP_X_FORWARDED_FOR', self::XIP_HT_ClientIP, true),
	//X.X.X.X, X.X.X.X
	array('HTTP_X_FORWARDED', self::XIP_HT_PInfo, true),
	//?
	array('HTTP_FORWARDED_FOR', self::XIP_HT_ClientIP,true),
	//?
	array('HTTP_FORWARDED', self::XIP_HT_PInfo, true),
	//by http://proxy.example.com:8080 (Netscape-Proxy/3.5)
	array('HTTP_X_COMING_FROM', self::XIP_HT_ClientIP, true),
	//?
	array('HTTP_COMING_FROM', self::XIP_HT_ClientIP, true),

	/*
	 HTTP_CLIENT_IP can be sometimes wrong (maybe if proxy chains used)
	 First look at HTTP_X_FORWARDED_FOR if exists (it can contain multiple IP addresses comma seperated)
	 (This is why HTTP_CLIENT_IP is written after HTTP_X_FORWARDED_FOR)
	 */
	array('HTTP_CLIENT_IP',
	self::XIP_HT_ClientIP,
	true),
	//X.X.X.X
	array('HTTP_PC_REMOTE_ADDR',
	self::XIP_HT_ClientIP,
	true),
	//X.X.X.X
	array('HTTP_CLIENTADDRESS',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_CLIENT_ADDRESS',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_SP_HOST',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_SP_CLIENT',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_ORIGINAL_HOST',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_ORIGINAL_REMOTE_ADDR',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_ORIG_CLIENT',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_CISCO_BBSM_CLIENTIP',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_AZC_REMOTE_ADDR',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_10_0_0_0',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_PROXY_AGENT',
	self::XIP_HT_PName,
	true),
	array('HTTP_X_SINA_PROXYUSER',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_XXX_REAL_IP',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_REMOTE_ADDR',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_RLNCLIENTIPADDR',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_REMOTE_HOST_WP',
	self::XIP_HT_ClientIP,
	true),
	array('HTTP_X_HTX_AGENT',
	self::XIP_HT_PName,
	true),
	array('HTTP_XONNECTION',
	self::XIP_HT_None,
	true),
	array('HTTP_X_LOCKING',
	self::XIP_HT_None,
	true),
	array('HTTP_PROXY_AUTHORIZATION',
	self::XIP_HT_None,
	true),
	array('HTTP_MAX_FORWARDS',
	self::XIP_HT_None,
	true),

	//array('HTTP_FROM', self::XIP_HT_ClientIP, true,'value'=>'/(\d{1,3}\.){3}\d{1,3}/'), //proxy is detected if header contains IP
	array('HTTP_X_IWPROXY_NESTING',
	self::XIP_HT_None,
	true),
	array('HTTP_X_TEAMSITE_PREREMAP',
	self::XIP_HT_None,
	true),
	//http://www.example.com/example...
	array('HTTP_X_SERIAL_NUMBER',
	self::XIP_HT_None,
	true),
	array('HTTP_CACHE_INFO',
	self::XIP_HT_None,
	true),
	array('HTTP_X_BLUECOAT_VIA',
	self::XIP_HT_PName,
	true),

	//search inside REMOTE_HOST
	/*
	 REMOTE_HOST can always be empty whether or not you have a host name,
	 This is because hostname lookups are turned off by default in many web hosting setups
	 look at here for more info and solutions => http://www.php.net/manual/en/function.gethostbyaddr.php
	 */
	//Yes, if remote host contains something like proxy123.example.com
	array('REMOTE_HOST',
	self::XIP_HT_None,
	true,
             'value'=>'/proxy.*\..*\..*/'),

	//Yes, if remote host contains something like cache123.example.com
	array('REMOTE_HOST',
	self::XIP_HT_None,
	true,
             'value'=>'/cache.*\..*\..*/'),

	//Guess Unknown headers using Regular expressions
	//array('/^HTTP_X_.*/', self::XIP_HT_None, true),
	array('/^HTTP_X_.*/',
	self::XIP_HT_ClientIP,
	true),
	array('/^HTTP_PROXY.*/',
	self::XIP_HT_None,
	true),
	array('/^HTTP_XROXY.*/',
	self::XIP_HT_None,
	true),
	array('/^HTTP_XPROXY.*/',
	self::XIP_HT_None,
	true),
	array('/^HTTP_VIA.*/',
	self::XIP_HT_None,
	false),
	array('/^HTTP_XXX.*/',
	self::XIP_HT_None,
	false),
	array('/^HTTP_XCACHE.*/',
	self::XIP_HT_None,
	false));
	/**
	 *   HINT ! :
	 *   If a HTTP Request Header sent as "tesT-someTHinG_aNYthing: hELLo",
	 *   PHP will set $_SERVER['HTTP_TEST_SOMETHING_ANYTHING']=hELLo
	 *   (As in PHP 4.3x installed as CGI module Apache)
	 */

	//INTERNAL USE - IP pattern

	private $ipp = '';


	/////////////////////////////////////////////////////////////////////
	// USER FUNCTIONS (PUBLIC)
	/////////////////////////////////////////////////////////////////////
	/**
	 *   This constructor function
	 *   Fills all useful information to $XIP->IP[] and $XIP->Proxy[] arrays for user
	 *   No need to call any functions later for IP detection or proxy detection (see example files)
	 *   However you can call some IP fuctions if you need
	 */
	public function __construct()
	{
		if (!isset($_SERVER['REMOTE_ADDR'])) {
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		}
		//INTERNAL USE - Some temporary variables used internally, do not touch...
		$ips = '';
		$ipa = array($_SERVER['REMOTE_ADDR']);

		$this->Proxy['detected'] = false;
		$this->Proxy['suspicious'] = false;
		$this->Proxy['name'] = NULL;
		$this->Proxy['info'] = array();
		$this->Proxy['headers'] = array();

		//INTERNAL USE - IP octet pattern
		$ipo = '(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)';
		//INTERNAL USE - IP pattern
		$this->ipp = "$ipo\\.$ipo\\.$ipo\\.$ipo";

		//If filename specified, Load Extended Private IP Address List from this file
		if (!empty($this->exfile)) {
			$tmp = implode('', file($this->exfile));
			if ( (!$tmp === false) && (preg_match('/'.$this->ipp.'/', $tmp)))
			$this->Private_IP_Extended = $tmp;
		}

		/**
		 *   EVALUATE $Proxy_Evidence array on HEADERS !!!
		 */
		foreach ($this->Proxy_Evidence as $value) {
			$tmp = $this->FindHeaders($value[0]);
			foreach ($tmp as $hkey=>$hvalue) {
				//make decision on proxy certainty data
				$pkey = ($value[2] === true) ? 'detected' : 'suspicious';
				if (array_key_exists('value', $value)) {
					if (preg_match($value['value'], $hvalue))
					$this->Proxy[$pkey] = true;
				} else {
					$this->Proxy[$pkey] = true;
				}
				//collect data about client IP, proxy name, other info
				if ($value[1] == self::XIP_HT_PName && empty ($this->Proxy['name']))
				$this->Proxy['name'] = $hvalue;
				if ($value[1] == self::XIP_HT_ClientIP)
				$ips .= $hvalue.',';//ips will be parsed later; also headers can contain multiple IP addresses
				if ( ($value[1] == self::XIP_HT_PInfo) || ($value[1] == self::XIP_HT_PName))
				$this->Proxy['info'][$hkey] = $hvalue;
				$this->Proxy['headers'][$hkey] = $hvalue;
			}
		}
		//both 'detected' and 'suspicious' cannot be true
		if ($this->Proxy['detected'])
		$this->Proxy['suspicious'] = false;

		//Fill $XIP->IP['proxy'] with REMOTE_ADDR always
		$this->IP['proxy'] = $_SERVER['REMOTE_ADDR'];
		$this->IP['client'] = '';//multiple call safe (with different options if any)
		//make decision on client IP
		if (preg_match_all('/'.$this->ipp.'/', $ips, $match))
		foreach ($match[0] as $value) {
			if (!in_array($value, $ipa))
			$ipa[] = $value;
			//Set the first public IP found as client IP
			//if ($this->isPublic($value) && empty($this->IP['client'])) $this->IP['client']=$value;
			$network = ($this->ex_proxy) ? $this->Private_IP_Extended : $this->Private_IP_Normal;
			if (!$this->CheckNet($network, $value) && empty ($this->IP['client']))
			$this->IP['client'] = $value;
		}
		//Fill $XIP->IP['all'] with REMOTE_ADDR and all IP addresses comma separated
		$this->IP['all'] = implode(",", $ipa);
		//Fill $XIP->IP['client'] with REMOTE_ADDR if client IP or proxy not detected
		if ( empty ($this->IP['client']))
		$this->IP['client'] = $this->IP['proxy'];
		$this->Proxy['anonymous'] = ($this->IP['client'] == $this->IP['proxy']) ? true : false;

	} // end constructor


	/**
	 *   Function to check if IP is a Public IP or not.
	 *
	 *   @param string $ip ip address
	 *   @return true if IP is a Public IP and false if IP is not a Public IP (and false if IP is not valid)
	 *   IT IS INVERSE OF $XIP->isPrivate() FUNCTION BELOW
	 */
	public function isPublic($ip)
	{
		if (!$this->isValid($ip))
		return false;
		return !$this->isPrivate($ip);
	}


	/**
	 *   Function to check if IP is a Private IP (belongs to a Local Network) or not.
	 *
	 *   @param string $ip ip address
	 *   @return true if IP is a Private IP and false if IP is not a Private IP (and false if IP is not valid)
	 *   Example: if ($XIP->isPrivate('172.25.66.7')) echo "ip belongs to local netwok"; //will output "ip belongs to local netwok"
	 */
	public function isPrivate($ip)
	{
		if (!$this->isValid($ip))
		return false;
		$networks = ($this->ex_private) ? $this->Private_IP_Extended : $this->Private_IP_Normal;
		return $this->CheckNet($networks, $ip);
	}


	/**
	 *   Function to check if IP is Valid. (IPv4)
	 *
	 *   @param string $ip ip address
	 *   @return bool Returns true if IP is valid and false if IP is not valid
	 *   Does a syntax and range check on IP.
	 *   Validates only dotted decimal IP notation 0.0.0.0-255.255.255.255 (without leading zeros or octal syntax)
	 *   Example: if ($XIP->isValid('127.0.0.1')) echo "ip is valid"; //will output "ip is valid"
	 */
	public function isValid($ip)
	{
		return (preg_match('/^'.$this->ipp.'$/', trim($ip)) > 0);
	}

	/**
	 *   Function to check if IP belongs to given Network or not
	 *   This function can be used for IP BLACKLIST, WHITELIST check
	 *
	 *   boolean CheckNet(mixed networks, string ip)
	 *   @param $networks parameter can be a string, comma separated strings,one string per line or array of strings in this format "IP[/MASK]", which you prefer...
	 *   @param $ip parameter refers to client IP by default; it is usually omitted on BLACKLIST, WHITELIST check usage; see examples below..
	 *   Example:
	 *            if ($XIP->CheckNet('127.0.0.0/255.255.255.0','127.0.0.5')) echo "IP belongs to given network"; //will output "ip belongs to given network"
	 *
	 *            * IP Range Check examples *:
	 *            //If you want to check if IP is in range of 192.168.2.0 to 192.168.2.255
	 *            if ($XIP->CheckNet('192.168.2.0/255.255.255.0')) echo "YES IN RANGE"; //check the client IP
	 *            if ($XIP->CheckNet('192.168.2.0/255.255.255.0',$ip)) echo "YES IN RANGE"; //check the given IP
	 *            //If you want to check if IP is in range of 192.168.0.0 to 192.168.255.255
	 *            if ($XIP->CheckNet('192.168.2.0/255.255.0.0')) echo "YES IN RANGE"; //check the client IP
	 *            if ($XIP->CheckNet('192.168.2.0/255.255.0.0',$ip)) echo "YES IN RANGE"; //check the given IP
	 *
	 *            * IP BLACKLIST, WHITELIST examples *:
	 *            $blacklist="10.0.5.0, 10.0.5.1, 10.0.5.2, 10.0.4.0/24, 10.0.3.0/255.255.255.0";
	 *            if ($XIP->CheckNet($blacklist)) echo "IP is in BLACKLIST"; //check the client IP
	 */
	public function CheckNet($networks, $ip = '')
	{
		//use client IP as default if $ip parameter omitted (see BL_Safe for more explanations)
		if ( empty ($ip))
		$ip = ($this->BL_Safe) ? $this->IP['proxy'] : $this->IP['client'];
		if (!$this->isValid($ip))
		return false;
		$ipl = ip2long(trim($ip));
		//if ($ipl===false) $ipl=-1;//ip2long returns FALSE for 255.255.255.255, convert it to its real value -1 (only PHP 5.0.0 < 5.0.3)
		$ips = (is_array($networks)) ? $networks : preg_split('/[\s,]+/', $networks);
		foreach ($ips as $value) {
			if (preg_match('/^'.$this->ipp.'(\/\d|\/'.$this->ipp.')?/', $value)) {
				$ipa = explode('/', $value);
				if (count($ipa) < 2)
				$ipa[1] = '';//prevent notice level error (I've added this line for some users reported notice level errors)
				$net = ip2long($ipa[0]);
				//if ($net===false) $net=-1;
				$x = ip2long($ipa[1]);
				//if ($x===false) $x=-1;
				$mask = long2ip($x) == $ipa[1] ? $x : 0xffffffff << (32 - $ipa[1]);
				if ( ($ipl & $mask) == ($net & $mask))
				return true;
			}
		}
		return false;
	}

	/**
	 *   This function name alias is deprecated and stands for backward compatibility.
	 *   Parameter order changed and $ip became optional in new CheckNet() function explained above.
	 *   Use CheckNet() function instead of this!
	 */
	function NetCheck($ip, $networks)
	{
		return $this->CheckNet($networks, $ip);
	}


	/**
	 *   INTERNAL USE - Regular Expression compatible Find Headers function
	 *   To Query existence of a request header and returns found headers
	 */
	private function FindHeaders($name)
	{
		$result = array();
		//if ($name[0]<>'/') $name='/^'.$name.'$/';//If regex not found convert it to an "exact phrase" regex as default
		//Return header directly if not written as regular expression (separated for speed)
		if ($name[0] <> '/') {
			if (array_key_exists($name, $_SERVER))
			$result[$name] = $_SERVER[$name];
		} else {
			//Regular expression headers search
			foreach ($_SERVER as $key=>$value) {
				if (preg_match($name, $key, $match))
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * Extract ip address from last element of email header 'received' array.
	 *
	 */
	public function fnExtractIp($strReceivedLine)
	{
		$strSenderIp = '';

		if (!empty($strReceivedLine)) {
			// tries to extract ip address or host from the received header.
			// it should be using the last received header in the email
			// extracted ip should be validated to make sure it's good and not a private ip
			$intIp = '';

			$strReceivedLine = preg_replace("/([\\t]+)/", ' ', $strReceivedLine);
			$strReceivedLine = preg_replace("/(\()([\\s]+)(\[)/",
                                             '([',
			$strReceivedLine);
			$strReceivedLine = preg_replace("/(::ffff:)/", '', $strReceivedLine);

			$result = array('ip'=>null, 'helo'=>null, 'by'=>null);
			// Not interested in ID part
			// BY part
			if (preg_match("/by /", $strReceivedLine)) {
				preg_match("/^by (\S+)/", $strReceivedLine, $matches);
				$result['by'] = (!empty($matches['1'])) ? $matches['1'] : '';
			}
			if (preg_match("/ by /", $strReceivedLine)) {
				preg_match("/ by (\S+)/", $strReceivedLine, $matches);
				$result['by'] = $matches['1'];
			}

			// FROM part
			if (preg_match("/from /", $strReceivedLine) && !preg_match("/from userid/",
			$strReceivedLine)) {

				if (preg_match("/^from \[(\S+)\] \(\\S+\ helo=(\S+)\)/",
				$strReceivedLine,
				$matches)) {
					$result['helo'] = $matches['2'];
					$result['ip'] = $matches['1'];
				} else
				if (preg_match("/^from \[(\S+)\]	\(\S+ \[(\S+)\]/",
				$strReceivedLine,
				$matches)) {
					$result['helo'] = $matches[1];
					$result['ip'] = $matches[2];
				} else
				if (preg_match("/^from (\S+) \(\S+ \[(\S+)\]/",
				$strReceivedLine,
				$matches)) {
					$result['helo'] = $matches[1];
					$result['ip'] = $matches[2];
					// my
				} else
				if (preg_match("/^from (\S+) \(\[(\S+)\]/",
				$strReceivedLine,
				$matches)) {
					$result['helo'] = $matches[1];
					$result['ip'] = $matches[2];

				} else
				if (preg_match("/^from \((\S+) \(\[(\S+)\]/",
				$strReceivedLine,
				$matches)) {
					$result['helo'] = $matches[1];
					$result['ip'] = $matches[2];
					// my to just get IP
				} else
				if (preg_match("/^from (.*)\[(\S+)\]/", $strReceivedLine, $matches)) {
					$result['helo'] = $matches[1];
					$result['ip'] = $matches[2];
					// Fix for YahooGroups
				} else
				if (preg_match("/^from \[(\S+)\]/", $strReceivedLine, $matches)) {
					$result['ip'] = $matches[1];
				} else
				if (preg_match("/^from (\S+) by/", $strReceivedLine, $matches)) {
					$result['helo'] = $matches[1];
				}
			}

			$res_ip = gethostbyname($result['ip']);
			$res_helo = trim(gethostbyname($result['helo']), '[]');
			$res_by = trim(gethostbyname($result['by']), '[]');

			if ($this->isPublic($res_ip)) {
				$intIp = $res_ip;
			} else
			if ($this->isPublic($res_helo)) {
				$intIp = $res_helo;
			} else
			if ($this->isPublic($res_by)) {
				$intIp = $res_by;
			}

			if ($intIp != '') {
				$strSenderIp = $intIp;
			}

		}

		return $strSenderIp;

	}

	/**
	 * If strIp is not an IP address,
	 * then try to get IP address using gethostbyname()
	 * if it failes, return false
	 *
	 * @param string $strIp IPv4 address or hostname
	 * @return mixed IPv4 address or false
	 */
	public final static function parseIpString($strIp = '')
	{
		if ( empty($strIp)) {

			return false;
		}

		if (false === \ip2long($strIp)) {
			$ip = \gethostbyname($strIp);

			if ($ip == $strIp) {
				e($strIp. 'Looks like invalid ip address');
				
				return false;
			}

			$strIp = $ip;
		}

		return filter_var($strIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

	}

} // end class
