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
use Jeboehm\Lampcp\CoreBundle\Entity\Dns;
use Jeboehm\Lampcp\CoreBundle\Form\Type\DnsSoaType;
use Jeboehm\Lampcp\CoreBundle\Form\Type\DnsType;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection\ZoneCollection;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\NS;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DnsController
 *
 * @package Jeboehm\Lampcp\CoreBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 *
 * @Route("/config/dns")
 */
class DnsController extends AbstractController {
    /**
     * Get a default zone for new entries
     *
     * @return ZoneCollection
     */
    protected function _getDefaultZone() {
        $nsdefault = $this
            ->_getConfigService()
            ->getParameter('dns.default.ns');
        $zone      = new ZoneCollection();
        $ns        = new NS();
        $soa       = new SOA();

        $soa
            ->setPrimary($nsdefault)
            ->setMail($this
                ->getUser()
                ->getEmail() . '.');

        $ns->setRdata($nsdefault);

        $zone
            ->add($soa)
            ->add($ns);

        return $zone;
    }

    /**
     * Lists all DNS entities.
     *
     * @Route("/", name="config_dns")
     * @Template()
     */
    public function indexAction() {
        /** @var $entities Dns[] */
        $entities = $this
            ->_getRepository()
            ->findBy(array('domain' => $this->_getSelectedDomain()), array('subdomain' => 'asc'));

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a DNS entity.
     *
     * @Route("/{entity}/show", name="config_dns_show")
     * @Template()
     */
    public function showAction(Dns $entity) {
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new DNS entity.
     *
     * @Route("/new", name="config_dns_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Dns($this->_getSelectedDomain());
        $entity->setZonecollection($this->_getDefaultZone());
        $form = $this->createForm(new DnsType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new DNS entity.
     *
     * @Route("/create", name="config_dns_create")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Dns:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Dns($this->_getSelectedDomain());
        $entity->setZonecollection($this->_getDefaultZone());

        $form = $this->createForm(new DnsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_dns_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DNS entity.
     *
     * @Route("/{entity}/edit", name="config_dns_edit")
     * @Template()
     */
    public function editAction(Dns $entity) {
        $editForm = $this->createForm(new DnsType(), $entity);

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Edits an existing DNS entity.
     *
     * @Route("/{entity}/update", name="config_dns_update")
     * @Method("POST")
     * @Template("JeboehmLampcpCoreBundle:Dns:edit.html.twig")
     */
    public function updateAction(Request $request, Dns $entity) {
        $editForm = $this->createForm(new DnsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $zone = $entity->getZonecollection();
            $zone
                ->getSoa()
                ->refreshSerial();
            $entity->setZonecollection(clone $zone);

            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_dns_edit', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Protection entity.
     *
     * @Route("/{entity}/delete", name="config_dns_delete")
     */
    public function deleteAction(Dns $entity) {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('config_dns'));
    }

    /**
     * Edit SOA record
     *
     * @Route("/{entity}/editsoa", name="config_dns_edit_soa")
     * @Template()
     */
    public function editSoaAction(Dns $entity) {
        $editForm = $this->createForm(new DnsSoaType(), $entity
            ->getZonecollection()
            ->getSoa());

        return array(
            'edit_form' => $editForm->createView(),
            'entity'    => $entity,
        );
    }

    /**
     * Update SOA record
     *
     * @Route("/{entity}/updatesoa", name="config_dns_update_soa")
     * @Template()
     */
    public function updateSoaAction(Request $request, Dns $entity) {
        $zone     = $entity->getZonecollection();
        $soa      = $zone->getSoa();
        $editForm = $this->createForm(new DnsSoaType(), $soa);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $soa->refreshSerial();

            $zone->add($soa);
            $entity->setZonecollection(clone $zone);

            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_dns_show', array('entity' => $entity->getId())));
        }

        return array(
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Get repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function _getRepository() {
        return $this
            ->getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:Dns');
    }
}
