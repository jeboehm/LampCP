<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord;

/**
 * Class SOA
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class SOA extends AbstractResourceRecord {
    /** @var string */
    protected $primary;

    /** @var string */
    protected $mail;

    /** @var integer */
    protected $serial;

    /** @var integer */
    protected $refresh;

    /** @var integer */
    protected $retry;

    /** @var integer */
    protected $expire;

    /** @var integer */
    protected $minimum;

    /**
     * Constructor
     *
     * @see http://www.ripe.net/ripe/docs/ripe-203
     */
    public function __construct() {
        parent::__construct();

        $this
            ->refreshSerial()
            ->setRefresh(86400)
            ->setRetry(7200)
            ->setExpire(3600000)
            ->setMinimum(172800);
    }

    /**
     * Set zone expire time
     *
     * @param int $expire
     *
     * @return SOA
     */
    public function setExpire($expire) {
        $this->expire = $expire;

        return $this;
    }

    /**
     * Get zone expire time
     *
     * @return int
     */
    public function getExpire() {
        return $this->expire;
    }

    /**
     * Set mailaddress
     *
     * @param string $mail
     *
     * @return SOA
     */
    public function setMail($mail) {
        $mail       = str_replace(array('@'), array('.'), $mail);
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mailaddress
     *
     * @return string
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * Set minimum time between refresh
     *
     * @param int $minimum
     *
     * @return SOA
     */
    public function setMinimum($minimum) {
        $this->minimum = $minimum;

        return $this;
    }

    /**
     * Get minimum time between refresh
     *
     * @return int
     */
    public function getMinimum() {
        return $this->minimum;
    }

    /**
     * Set primary nameserver
     *
     * @param string $primary
     *
     * @return SOA
     */
    public function setPrimary($primary) {
        $this->primary = $primary;

        return $this;
    }

    /**
     * Get primary nameserver
     *
     * @return string
     */
    public function getPrimary() {
        return $this->primary;
    }

    /**
     * Set refresh interval
     *
     * @param int $refresh
     *
     * @return SOA
     */
    public function setRefresh($refresh) {
        $this->refresh = $refresh;

        return $this;
    }

    /**
     * Get refresh interval
     *
     * @return int
     */
    public function getRefresh() {
        return $this->refresh;
    }

    /**
     * Set retry time
     *
     * @param int $retry
     *
     * @return SOA
     */
    public function setRetry($retry) {
        $this->retry = $retry;

        return $this;
    }

    /**
     * Get retry time
     *
     * @return int
     */
    public function getRetry() {
        return $this->retry;
    }

    /**
     * Set serial to track changes
     *
     * @param int $serial
     *
     * @return SOA
     */
    public function setSerial($serial) {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial to track changes
     *
     * @return int
     */
    public function getSerial() {
        return $this->serial;
    }

    /**
     * Sets new serial
     *
     * @return SOA
     */
    public function refreshSerial() {
        $date = date('Ymd');

        if (substr($this->getSerial(), 0, strlen($date)) == $date) {
            $incr = substr($this->getSerial(), strlen($date));
            $incr = $incr + 1;
        } else {
            $incr = 1;
        }

        $incr = str_pad($incr, 2, '0', STR_PAD_LEFT);
        $this->setSerial($date . $incr);

        return $this;
    }

    /**
     * Get class name
     *
     * @return string
     */
    protected function _getClassName() {
        return __CLASS__;
    }
}
