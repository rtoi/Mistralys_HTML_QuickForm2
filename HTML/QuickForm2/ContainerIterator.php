<?php
/**
 * Implements a recursive iterator for the container elements
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
 * Implements a recursive iterator for the container elements
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 *
 * @extends RecursiveArrayIterator<int,HTML_QuickForm2_Node>
 */
class HTML_QuickForm2_ContainerIterator extends RecursiveArrayIterator
{
    public function __construct(HTML_QuickForm2_Container $container)
    {
        parent::__construct($container->getElements());
    }

    public function hasChildren() : bool
    {
        return $this->current() instanceof HTML_QuickForm2_Container;
    }

    /**
     * @return HTML_QuickForm2_ContainerIterator
     * @throws HTML_QuickForm2_InvalidArgumentException
     * @throws HTML_QuickForm2_NotFoundException
     */
    public function getChildren() : HTML_QuickForm2_ContainerIterator
    {
        return new HTML_QuickForm2_ContainerIterator(HTML_QuickForm2_Loader::requireObjectInstanceOf(
            HTML_QuickForm2_Container::class,
            $this->current()
        ));
    }
}
