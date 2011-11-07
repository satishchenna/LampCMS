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


namespace Lampcms;

use \Lampcms\Api\Output\Formatter;

/**
 * Class for storing a data
 * ready for the HTTP Response
 *
 * Loosely modeled after the Response class
 * from the Symfony2 Framework but there are some
 * important differences - namely the use of Output class
 * and simpler handling of headers
 *
 * @author Dmitri Snytkine
 *
 */
class Response
{
	protected $aHeaders = array();

	protected $httpCode = 200;

	protected $httpMessage = '';

	protected $charset = 'UTF-8';

	protected $body = '';

	protected $httpVersion = '1.1';

	protected $contentType = 'text/html';

	public static function factory(){
		return new static();
	}

	protected $aResponses = array(
	200 => 'OK',
	201 => 'Created',
	202 => 'Accepted',
	203 => 'Non-Authoritative Information',
	204 => 'No Content',
	205 => 'Reset Content',
	206 => 'Partial Content',
	300 => 'Multiple Choices',
	301 => 'Moved Permanently',
	302 => 'Found',
	303 => 'See Other',
	304 => 'Not Modified',
	305 => 'Use Proxy',
	307 => 'Temporary Redirect',
	400 => 'Bad Request',
	401 => 'Unauthorized',
	402 => 'Payment Required',
	403 => 'Forbidden',
	404 => 'Not Found',
	405 => 'Method Not Allowed',
	406 => 'Not Acceptable',
	407 => 'Proxy Authentication Required',
	408 => 'Request Timeout',
	409 => 'Conflict',
	410 => 'Gone',
	411 => 'Length Required',
	412 => 'Precondition Failed',
	413 => 'Request Entity Too Large',
	414 => 'Request-URI Too Long',
	415 => 'Unsupported Media Type',
	416 => 'Requested Range Not Satisfiable',
	417 => 'Expectation Failed',
	500 => 'Internal Server Error',
	501 => 'Not Implemented',
	502 => 'Bad Gateway',
	503 => 'Service Unavailable',
	504 => 'Gateway Timeout',
	505 => 'HTTP Version Not Supported',
	);


	/**
	 * Setter for $this->body
	 *
	 * @param mixed $body string | object that has __toString()
	 * method
	 *
	 * @return object $this
	 */
	public function setBody($body){
		$this->body = (string)$body;

		return $this;
	}


	/**
	 * Getter for body
	 *
	 * @return string
	 */
	public function getBody(){
		return $this->body;
	}


	/**
	 * Getter for headers array
	 *
	 * @return array
	 */
	public function getHeaders(){
		return $this->aHeaders;
	}


	public function setContentType($contentType){
		$this->contentType = $contentType;

		return $this;
	}


	/**
	 * Accept the Output object
	 * and use it to set the
	 * Content-Type header
	 * and the body of response
	 *
	 *
	 * @param Output $o instance of Output object
	 */
	public function setOutput(Formatter $o){
		$type = $o->getContentType();
		$this->contentType = $type;
		d('setting Content-Type header to: '.$type);

		$this->setBody($o);

		return $this;
	}


	/**
	 * Setter for httpCode
	 *
	 * @param int $code
	 *
	 * @return object $this
	 *
	 */
	public function setHttpCode($code, $message = null){


		if(!\is_int($code)){
			throw new \InvalidArgumentException('Param $code must be integer. Was: '.gettype($code));
		}

		if($code < 100 || $code >= 600){
			throw new \InvalidArgumentException('Param $code is not a valid HTTP Response code. Was: '.$code);
		}

		$this->httpCode = $code;

		$this->httpMessage = (false === $message) ? '' : ( (null === $message && !empty($this->aResponses[$this->httpCode])) ? $this->aResponses[$this->httpCode] : (string)$message);

		return $this;
	}


	/**
	 * Setter for httpMessage
	 *
	 * @param string $message
	 *
	 * @return object $this
	 */
	public function setHttpMessage($message){
		$this->httpMessage = $message;

		return $this;
	}



	public function setHeaders(array $headers){
		$this->aHeaders = $headers;

		return $this;
	}


	/**
	 * Add one header to array aHeaders
	 *
	 * @param string $name name of header
	 * @param string $val value of header
	 *
	 * @return object $this
	 *
	 */
	public function addHeader($name, $val){
		$this->aHeaders[$name] = $val;

		return $this;
	}


	/**
	 * Setter for charset
	 *
	 * @param string $charset
	 *
	 * @return object $this
	 */
	public function setCharset($charset){
		$this->charset = $charset;

		return $this;
	}


	/**
	 * Send headers followed by
	 * the body to the browser
	 *
	 */
	public function send(){
		$this->sendHeaders();
		$this->sendBody();
	}


	/**
	 * Sets the HTTP protocol version (1.0 or 1.1).
	 *
	 * @param string $version The HTTP protocol version
	 */
	public function setHttpVersion($version){
		$this->version = $version;

		return $this;
	}


	/**
	 * Send out http headers
	 *
	 */
	public function sendHeaders(){
		if(headers_sent($file, $line)){
			d('Headers already sent in '.$file.' line '.$line);
		}
		d('before sending out api resonse. $this->contentType: '.$this->contentType.' $this->aHeaders: '.print_r($this->aHeaders, 1));
		\header(sprintf('HTTP/%s %s %s', $this->httpVersion, $this->httpCode, $this->httpMessage));
		\header('Content-Type: '.$this->contentType.'; charset='.strtolower($this->charset));

		foreach ($this->aHeaders as $name => $value) {
			\header($name.': '.$value);
		}
	}


	/**
	 * Send out the body to the browser
	 *
	 */
	public function sendBody(){
		echo $this->body;
	}

}
