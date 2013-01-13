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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * Certificate
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class Certificate {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @var Domain[]
	 * @OneToMany(targetEntity="Domain", mappedBy="certificate")
	 */
	private $domain;

	/**
	 * @var Subdomain[]
	 * @OneToMany(targetEntity="Subdomain", mappedBy="certificate")
	 */
	private $subdomain;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificateFile", type="text")
	 */
	private $CertificateFile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificateFilePath", type="string", length=255)
	 */
	private $CertificateFilePath;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificateKeyFile", type="text")
	 */
	private $CertificateKeyFile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificateKeyFilePath", type="string", length=255)
	 */
	private $CertificateKeyFilePath;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificateChainFile", type="text")
	 */
	private $CertificateChainFile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificateChainFilePath", type="string", length=255)
	 */
	private $CertificateChainFilePath;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CACertificateFile", type="text")
	 */
	private $CACertificateFile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CACertificateFilePath", type="string", length=255)
	 */
	private $CACertificateFilePath;

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->CACertificateFile        = '';
		$this->CertificateChainFile     = '';
		$this->CertificateKeyFile       = '';
		$this->CertificateFilePath      = '';
		$this->CertificateKeyFilePath   = '';
		$this->CACertificateFilePath    = '';
		$this->CertificateChainFilePath = '';
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Certificate
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set CertificateFile
	 *
	 * @param string $certificateFile
	 *
	 * @return Certificate
	 */
	public function setCertificateFile($certificateFile) {
		$this->CertificateFile = strval($certificateFile);

		return $this;
	}

	/**
	 * Get CertificateFile
	 *
	 * @return string
	 */
	public function getCertificateFile() {
		return $this->CertificateFile;
	}

	/**
	 * Set CertificateKeyFile
	 *
	 * @param string $certificateKeyFile
	 *
	 * @return Certificate
	 */
	public function setCertificateKeyFile($certificateKeyFile) {
		$this->CertificateKeyFile = strval($certificateKeyFile);

		return $this;
	}

	/**
	 * Get CertificateKeyFile
	 *
	 * @return string
	 */
	public function getCertificateKeyFile() {
		return $this->CertificateKeyFile;
	}

	/**
	 * Set CertificateChainFile
	 *
	 * @param string $certificateChainFile
	 *
	 * @return Certificate
	 */
	public function setCertificateChainFile($certificateChainFile) {
		$this->CertificateChainFile = strval($certificateChainFile);

		return $this;
	}

	/**
	 * Get CertificateChainFile
	 *
	 * @return string
	 */
	public function getCertificateChainFile() {
		return $this->CertificateChainFile;
	}

	/**
	 * Set CACertificateFile
	 *
	 * @param string $cACertificateFile
	 *
	 * @return Certificate
	 */
	public function setCACertificateFile($cACertificateFile) {
		$this->CACertificateFile = strval($cACertificateFile);

		return $this;
	}

	/**
	 * Get CACertificateFile
	 *
	 * @return string
	 */
	public function getCACertificateFile() {
		return $this->CACertificateFile;
	}

	/**
	 * Get Domains
	 *
	 * @return Domain[]
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Get Subdomains
	 *
	 * @return Subdomain[]
	 */
	public function getSubdomain() {
		return $this->subdomain;
	}

	/**
	 * @param string $CACertificateFilePath
	 *
	 * @return Certificate
	 */
	public function setCACertificateFilePath($CACertificateFilePath) {
		$this->CACertificateFilePath = strval($CACertificateFilePath);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCACertificateFilePath() {
		return $this->CACertificateFilePath;
	}

	/**
	 * @param string $CertificateChainFilePath
	 *
	 * @return Certificate
	 */
	public function setCertificateChainFilePath($CertificateChainFilePath) {
		$this->CertificateChainFilePath = strval($CertificateChainFilePath);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCertificateChainFilePath() {
		return $this->CertificateChainFilePath;
	}

	/**
	 * @param string $CertificateFilePath
	 *
	 * @return Certificate
	 */
	public function setCertificateFilePath($CertificateFilePath) {
		$this->CertificateFilePath = strval($CertificateFilePath);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCertificateFilePath() {
		return $this->CertificateFilePath;
	}

	/**
	 * @param string $CertificateKeyFilePath
	 *
	 * @return Certificate
	 */
	public function setCertificateKeyFilePath($CertificateKeyFilePath) {
		$this->CertificateKeyFilePath = strval($CertificateKeyFilePath);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCertificateKeyFilePath() {
		return $this->CertificateKeyFilePath;
	}
}
