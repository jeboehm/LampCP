<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType as ParentClassType;

abstract class AbstractType extends ParentClassType {
	protected $_isEditMode = false;

	/**
	 * Konstruktor
	 *
	 * @param bool $edit
	 */
	public function __construct($edit = false) {
		$this->_isEditMode = (bool)$edit;
	}

	protected function _getIsEditMode() {
		return $this->_isEditMode;
	}
}
