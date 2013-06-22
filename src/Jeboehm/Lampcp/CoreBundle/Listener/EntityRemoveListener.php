<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Jeboehm\Lampcp\CoreBundle\Entity\AbstractEntity;
use Jeboehm\Lampcp\CoreBundle\Service\BuilderNotifierService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityRemoveListener
 *
 * @package Jeboehm\Lampcp\CoreBundle\Listener
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class EntityRemoveListener
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets called when an entity is removed.
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AbstractEntity) {
            $this
                ->getBuilderNotifier()
                ->notifyBuilder($entity);
        }
    }

    /**
     * Get BuilderNotifier.
     *
     * @return BuilderNotifierService
     */
    protected function getBuilderNotifier()
    {
        return $this->container->get('jeboehm_lampcp_core.buildernotifierservice');
    }
}
