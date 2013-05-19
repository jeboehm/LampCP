<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Entity;

interface DomainInterface
{
    /**
     * Returns true, if this is an PHP enabled domain.
     *
     * @return bool
     */
    public function getParsePhp();

    /**
     * Get the user who owns the domain.
     *
     * @return User
     */
    public function getUser();
}