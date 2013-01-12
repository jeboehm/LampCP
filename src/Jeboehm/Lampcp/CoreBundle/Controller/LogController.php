<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Log controller.
 *
 * @Route("/config/log")
 */
class LogController extends AbstractController {
	/**
	 * Lists all Log entities.
	 *
	 * @Route("/", name="log")
	 * @Template()
	 */
	public function indexAction() {
		$entities = $this->_getLogger()->getLogs();

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
	}
}
