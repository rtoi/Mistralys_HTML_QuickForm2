<?php

declare(strict_types=1);

namespace QuickFormTests\DataSource;

use HTML\QuickForm2\DataSource\ManualSubmitDataSource;
use HTML_QuickForm2;
use HTML_QuickForm2_DataSource_Array;
use QuickFormTests\CaseClasses\QuickFormCase;

final class ManualSubmitTests extends QuickFormCase
{
    public function test_submitManuallyWithDataSource() : void
    {
        $form = new HTML_QuickForm2();

        $el = $form->addText('foo');
        $el->setValue('bar');

        $this->assertFalse($form->isSubmitted());
        $this->assertSame('bar', $el->getValue());

        $ds = new ManualSubmitDataSource();
        $ds->setValue('foo', 'baz');

        $form->submitManually($ds);

        $this->assertTrue($form->isSubmitted());
        $this->assertSame('baz', $el->getValue());
        $this->assertTrue($form->getDataReason()['manualSubmit']);
    }

    public function test_clearDataSources() : void
    {
        $form = new HTML_QuickForm2();
        $el = $form->addText('foo');

        $firstDS = new HTML_QuickForm2_DataSource_Array(array('foo' => 'first'));
        $form->addDataSource($firstDS);

        $this->assertSame('first', $el->getValue());

        // Adding another data source means that the first
        // data source is used if it has a value for an
        // element.
        $secondDS = new HTML_QuickForm2_DataSource_Array(array('foo' => 'second'));
        $form->addDataSource($secondDS);

        $this->assertSame('first', $el->getValue());

        // Clearing the data sources makes it possible to
        // ensure an order.
        $form->clearDataSources();

        $form->addDataSource($secondDS);
        $this->assertSame('second', $el->getValue());
    }
}
