<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Service;

class CryptService {
	const _CHECK_PATTERN = '##decryptme##';

	/** @var string */
	private $_key;

	/**
	 * Konstruktor
	 *
	 * @param string $key
	 */
	public function __construct($key) {
		$this->_key = $key;
	}

	/**
	 * Encrypt data
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	public function encrypt($data) {
		$data      = self::_CHECK_PATTERN . $data . self::_CHECK_PATTERN;
		$iv        = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB),
			MCRYPT_RAND);
		$passcrypt = trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,
			$this->_getKey(), trim($data), MCRYPT_MODE_ECB, $iv));
		$encode    = base64_encode($passcrypt);

		return $encode;
	}

	/**
	 * Decrypt data
	 *
	 * @param string $data
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function decrypt($data) {
		$decoded   = base64_decode($data);
		$iv        = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB),
			MCRYPT_RAND);
		$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_getKey(), trim($decoded),
			MCRYPT_MODE_ECB, $iv));

		if(substr_count($decrypted, self::_CHECK_PATTERN) !== 2) {
			throw new \Exception('Wrong encryption key used!');
		} else {
			$decrypted = str_replace(self::_CHECK_PATTERN, '', $decrypted);
		}

		return $decrypted;
	}

	/**
	 * Check for installed extension
	 *
	 * @throws \Exception
	 */
	protected function _doSanityChecks() {
		if(!function_exists('mcrypt_encrypt')) {
			throw new \Exception('Please install the mcrypt extension!');
		}
	}

	/**
	 * Get encryption key
	 *
	 * @return string
	 */
	protected function _getKey() {
		return $this->_key;
	}
}
