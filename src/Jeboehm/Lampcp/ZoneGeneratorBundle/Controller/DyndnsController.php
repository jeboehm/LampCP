<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Monolog\Logger;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\Controller\HostNotValid;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\Controller\NoHostsGiven;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\Controller\TooManyHosts;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\Controller\HostNotFound;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\DyndnsUpdateReturnCodes;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Service\RecordUpdateService;

/**
 * Class DyndnsController
 *
 * URL Parameters:
 * - myip (optional, IPv4 or IPv6)
 * - hostname (required, comma seperated)
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Controller
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 * @link    http://dyn.com/support/developers/api/perform-update/
 *
 * @Route("/nic")
 */
class DyndnsController extends Controller {
    /**
     * Update action
     *
     * @Route("/update")
     */
    public function updateAction() {
        $hostnameQuery = $this
            ->getRequest()
            ->get('hostname', null);
        $hostnames     = explode(',', $hostnameQuery);
        $ip            = $this->_getIpAddress($this->getRequest());

        try {
            if (count($hostnames) > 20) {
                throw new TooManyHosts();
            } elseif (count($hostnames) < 1 || (count($hostnames) == 1 && empty($hostnames[0]))) {
                throw new NoHostsGiven();
            }

            foreach ($hostnames as $hostname) {
                $result = $this
                    ->_getRecordUpdateService()
                    ->update($hostname, $ip);

                if (!$result) {
                    throw new HostNotFound();
                } else {
                    $this
                        ->_getLogger()
                        ->info(sprintf('Set %s to %s.', $hostname, $ip));
                }
            }
        } catch (TooManyHosts $e) {
            return new Response(DyndnsUpdateReturnCodes::TOO_MANY_HOSTS);
        } catch (NoHostsGiven $e) {
            return new Response(DyndnsUpdateReturnCodes::WRONG_DOMAIN_FORMAT);
        } catch (HostNotValid $e) {
            return new Response(DyndnsUpdateReturnCodes::WRONG_DOMAIN_FORMAT);
        } catch (HostNotFound $e) {
            return new Response(DyndnsUpdateReturnCodes::DOMAIN_NOT_FOUND);
        }

        return new Response(DyndnsUpdateReturnCodes::SUCCESS);
    }

    /**
     * Get IP address.
     * First, try 'myip' GET variable,
     * determine it automatically, when it's
     * not valid.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function _getIpAddress(Request $request) {
        $ip = $request->get('myip', '');

        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            // Automatically determine ip address
            $ip = $request->getClientIp();
        }

        return $ip;
    }

    /**
     * Get record update service.
     *
     * @return RecordUpdateService
     */
    protected function _getRecordUpdateService() {
        return $this->container->get('jeboehm_lampcp_zonegenerator.recordupdateservice');
    }

    /**
     * Get logger.
     *
     * @return Logger
     */
    protected function _getLogger() {
        return $this->container->get('logger');
    }
}