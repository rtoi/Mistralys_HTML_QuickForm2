<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Interfaces;

use HTML_QuickForm2_Node;
use Stringable;

interface ElementContainerInterface
{
    public function appendChild(HTML_QuickForm2_Node $element) : HTML_QuickForm2_Node;
    public function prependChild(HTML_QuickForm2_Node $element) : HTML_QuickForm2_Node;

    /**
     * @param string|HTML_QuickForm2_Node $elementOrType Either type name (treated case-insensitively) or an element instance
     * @param string|NULL $name Element name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes Element attributes
     * @param array<mixed> $data Element-specific data
     * @return HTML_QuickForm2_Node
     */
    public function addElement(
        $elementOrType, ?string $name = null, $attributes = null, array $data = array()
    ) : HTML_QuickForm2_Node;
    /**
     * @param string|HTML_QuickForm2_Node $elementOrType Either type name (treated case-insensitively) or an element instance
     * @param string|NULL $name Element name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes Element attributes
     * @param array<mixed> $data Element-specific data
     * @return HTML_QuickForm2_Node
     */
    public function prependElement($elementOrType, ?string $name = null, $attributes = null, array $data = array()) : HTML_QuickForm2_Node;

    /**
     * @return HTML_QuickForm2_Node[]
     */
    public function getElements() : array;

    /**
     * @param string $name
     * @return HTML_QuickForm2_Node[]
     */
    public function getElementsByName(string $name) : array;
    public function insertBefore(HTML_QuickForm2_Node $element, ?HTML_QuickForm2_Node $reference = null) : HTML_QuickForm2_Node;
    public function removeChild(HTML_QuickForm2_Node $element) : HTML_QuickForm2_Node;
    public function getElementById(string $id) : ?HTML_QuickForm2_Node;
    public function requireElementById(string $id) : HTML_QuickForm2_Node;
}
