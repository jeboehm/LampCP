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
 * CRUD Interface
 */
interface ICrudController extends ICrudAbstractController {
	/**
	 * Index
	 *
	 * @return mixed
	 */
	public function indexAction();

	/**
	 * New
	 *
	 * @return mixed
	 */
	public function newAction();

	/**
	 * Create
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return mixed
	 */
	public function createAction(Request $request);
}
