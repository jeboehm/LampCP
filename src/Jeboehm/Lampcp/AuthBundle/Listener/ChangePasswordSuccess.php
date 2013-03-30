<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\AuthBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;

/**
 * Class ChangePasswordSuccess
 *
 * Listens for password changing events.
 *
 * @package Jeboehm\Lampcp\AuthBundle\Listener
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ChangePasswordSuccess implements EventSubscriberInterface {
    /** @var UrlGeneratorInterface */
    private $router;

    /**
     * Constructor
     *
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    /**
     * Change password success
     *
     * @param FormEvent $event
     */
    public function onChangePasswordSuccess(FormEvent $event) {
        $url = $this->router->generate('status');

        $event->setResponse(new RedirectResponse($url));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess',
        );
    }
}