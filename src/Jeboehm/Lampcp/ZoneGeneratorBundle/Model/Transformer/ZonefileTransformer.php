<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer;

use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Collection\ZoneCollection;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\NSNotFound;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\TemplateNotFound;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\NoResourceRecords;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\NoSoaRecord;

class ZonefileTransformer {
    /** @var ZoneCollection */
    protected $_collection;

    /**
     * Constructor
     *
     * @param ZoneCollection $collection
     */
    public function __construct(ZoneCollection $collection) {
        $this->_collection = $collection;
    }

    /**
     * Transform zone to file
     *
     * @return string
     * @throws \Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\NoSoaRecord
     * @throws \Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\NSNotFound
     * @throws \Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\NoResourceRecords
     */
    public function transform() {
        $ns_origin = false;

        if (!$this->_collection->getSoa()) {
            throw new NoSoaRecord();
        }

        if ($this->_collection->count() < 1) {
            throw new NoResourceRecords();
        }

        foreach ($this->_collection->getByType('NS') as $rr) {
            if ($rr->getName() == '@') {
                $ns_origin = true;
            }
        }

        if (!$ns_origin) {
            throw new NSNotFound();
        }

        return $this->_createZone();
    }

    /**
     * Create zone
     *
     * @return string
     */
    protected function _createZone() {
        $content = array();
        $records = array_merge(array($this->_collection->getSoa()), $this->_collection->getValues());

        foreach ($records as $record) {
            /** @var $record AbstractResourceRecord */
            $template  = $this->_loadTemplate($record->getType());
            $variables = $this->_searchVariables($template);

            foreach ($variables as $var) {
                $replace  = call_user_func(array($record, sprintf('get%s', ucfirst($var))));
                $template = str_replace('%' . $var . '%', $replace, $template);
            }

            $content[] = $template;
        }

        return join(PHP_EOL, $content);
    }

    /**
     * Search for template variables
     *
     * @param string $template
     *
     * @return array
     */
    protected function _searchVariables($template) {
        $aktpos    = 0;
        $length    = strlen($template);
        $variables = array();

        while ($aktpos <= $length) {
            $posStart = strpos($template, '%', $aktpos) + 1;
            $posEnd   = strpos($template, '%', $posStart);

            if ($posEnd < $aktpos) {
                break;
            }

            $variables[] = substr($template, $posStart, ($posEnd - $posStart));
            $aktpos      = $posEnd + 2;
        }

        return $variables;
    }

    /**
     * Load Template
     *
     * @param string $type
     *
     * @return string
     * @throws \Jeboehm\Lampcp\ZoneGeneratorBundle\Exception\TemplateNotFound
     */
    protected function _loadTemplate($type) {
        $filename = $this->_getTemplateFilename($type);

        if (!file_exists($filename)) {
            $type     = 'default';
            $filename = $this->_getTemplateFilename($type);
        }

        if (!file_exists($filename)) {
            throw new TemplateNotFound();
        }

        return file_get_contents($filename);
    }

    /**
     * Get template filename
     *
     * @param string $type
     *
     * @return string
     */
    private function _getTemplateFilename($type) {
        return sprintf('%s/../Resources/template/%s.txt', __DIR__, strtolower($type));
    }
}
