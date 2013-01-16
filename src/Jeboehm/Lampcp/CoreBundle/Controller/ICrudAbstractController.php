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
 * Abstract CRUD Interface
 */
interface ICrudAbstractController {
	/**
	 * Show
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function showAction($id);

	/**
	 * Edit
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function editAction($id);

	/**
	 * Update
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int                                       $id
	 *
	 * @return mixed
	 */
	public function updateAction(Request $request, $id);

	/**
	 * Delete
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int                                       $id
	 *
	 * @return mixed
	 */
	public function deleteAction(Request $request, $id);
}
