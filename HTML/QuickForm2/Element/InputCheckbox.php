<?php
/**
 * Class for <input type="checkbox" /> elements
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to BSD 3-Clause License that is bundled
 * with this package in the file LICENSE and available at the URL
 * https://raw.githubusercontent.com/pear/HTML_QuickForm2/trunk/docs/LICENSE
 *
 * @category  HTML
 * @package   HTML_QuickForm2
 * @author    Alexey Borzov <avb@php.net>
 * @author    Bertrand Mansion <golgote@mamasam.com>
 * @copyright 2006-2020 Alexey Borzov <avb@php.net>, Bertrand Mansion <golgote@mamasam.com>
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      https://pear.php.net/package/HTML_QuickForm2
 */

declare(strict_types=1);

/**
 * Class for <input type="checkbox" /> elements
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_InputCheckbox extends HTML_QuickForm2_Element_InputCheckable
{
    protected array $attributes = array('type' => 'checkbox');

    protected $frozenHtml = array(
        'checked'   => '<code>[x]</code>',
        'unchecked' => '<code>[&nbsp;]</code>'
    );

    public function __construct($name = null, $attributes = null, array $data = array())
    {
        parent::__construct($name, $attributes, $data);
        if (null === $this->getAttribute('value')) {
            $this->setAttribute('value', 1);
        }
    }

    protected function updateValue() : void
    {
        if(!$this->hasDataSources()) {
            return;
        }

        $name = $this->getName();
        if ('[]' === substr($name, -2)) {
            $name = substr($name, 0, -2);
        }

        $ds = $this->resolveDataSourceByName($name, true);

        // *some* data sources were searched, but we did not find a value -> uncheck the box
        if(!$ds) {
            $this->removeAttribute('checked');
            return;
        }

        $value = $ds->getValue($name);
        if (!is_array($value)) {
            $this->setValue($value);
        } elseif (in_array($this->getAttribute('value'), array_map('strval', $value), true)) {
            $this->setAttribute('checked');
        } else {
            $this->removeAttribute('checked');
        }
    }

    public function setChecked(bool $checked=true) : self
    {
        if($checked) {
            $this->setAttribute('checked');
        } else {
            $this->removeAttribute('checked');
        }

        return $this;
    }
}
