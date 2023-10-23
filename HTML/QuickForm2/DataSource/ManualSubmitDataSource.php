<?php

declare(strict_types=1);

namespace HTML\QuickForm2\DataSource;

use HTML_QuickForm2_DataSource_Array;
use HTML_QuickForm2_DataSource_Submit;

/**
 * Array-based data source that can be used in place of
 * the {@see \HTML_QuickForm2_DataSource_SuperGlobal} source
 * to simulate a form submission with the {@see \HTML_QuickForm2::submitManually()}
 * method.
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ManualSubmitDataSource extends HTML_QuickForm2_DataSource_Array implements HTML_QuickForm2_DataSource_Submit
{
    public function getUpload(string $name): ?array
    {
        return null;
    }
}
