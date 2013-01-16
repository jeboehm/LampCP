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
use Symfony\Component\HttpFoundation\Request;

/**
 * CRUD Interface for Subcontrollers
 */
interface ICrudSubController extends ICrudAbstractController {
	/**
	 * Index
	 *
	 * @param int $parent
	 *
	 * @return mixed
	 */
	public function indexAction($parent);

	/**
	 * New
	 *
	 * @param int $parent
	 *
	 * @return mixed
	 */
	public function newAction($parent);

	/**
	 * Create
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int                                       $parent
	 *
	 * @return mixed
	 */
	public function createAction(Request $request, $parent);
}
