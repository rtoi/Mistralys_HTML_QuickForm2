<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use BaseHTMLElement;

/**
 * Let's just make parseAttributes() public rather than copy and paste regex
 */
abstract class TestSelectAttributeParser extends BaseHTMLElement
{
    public static function parseAttributes($attrString): array
    {
        return parent::parseAttributes($attrString);
    }
}
