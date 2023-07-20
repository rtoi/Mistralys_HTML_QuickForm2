<?php
/**
 * Usage example for HTML_QuickForm2 package: basic elements
 */

require_once __DIR__.'/_prepend.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <style type="text/css">
/* Set up custom font and form width */
body {
    margin-left: 10px;
    font-family: Arial,sans-serif;
    font-size: small;
}

.quickform {
    min-width: 500px;
    max-width: 600px;
    width: 560px;
}

/* Use default styles included with the package */

<?php
if ('@data_dir@' != '@' . 'data_dir@') {
    $filename = '@data_dir@/HTML_QuickForm2/quickform.css';
} else {
    $filename = dirname(dirname(__DIR__)) . '/data/quickform.css';
}
readfile($filename);
?>
    </style>
    <title>HTML_QuickForm2 basic elements example</title>
  </head>
  <body>
<?php

$options = array(
    'a' => 'Letter A', 'b' => 'Letter B', 'c' => 'Letter C',
    'd' => 'Letter D', 'e' => 'Letter E', 'f' => 'Letter F'
);

$main = array("Pop", "Rock", "Classical");

$secondary = array(
    array(0 => "Belle & Sebastian", 1 => "Elliot Smith", 2 => "Beck"),
    array(3 => "Noir Desir", 4 => "Violent Femmes"),
    array(5 => "Wagner", 6 => "Mozart", 7 => "Beethoven")
);

$form = new HTML_QuickForm2('elements');

// data source with default values:
$form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
    'textTest'        => 'Some text',
    'areaTest'        => "Some text\non multiple lines",
    'userTest'        => 'luser',
    'selSingleTest'   => 'f',
    'selMultipleTest' => array('b', 'c'),
    'boxTest'         => '1',
    'radioTest'       => '2',
    'testDate'        => time(),
    'testHierselect'  => array(2, 5)
)));

// text input elements
$fsText = $form->addFieldset()->setLabel('Text boxes');

$fsText->addText('textTest')
    ->setStyle('width: 300px')
    ->setLabel('Test text:');

$fsText->addPassword('pwdTest')
    ->setStyle('width: 300px')
    ->setLabel('Test Password:');

$area = $fsText->addTextarea('areaTest')
    ->setStyle('width:300px')
    ->setRows(7)
    ->setColumns(50)
    ->setLabel('Test Textarea:');

$fsNested = $form->addFieldset()->setLabel('Nested fieldset');

$fsNested->addText('userTest')
    ->setStyle('width: 200px')
    ->setLabel('Username:');

$fsNested->addPassword('passTest')
    ->setStyle('width: 200px')
    ->setLabel('Password:');

// Now we move the fieldset into another fieldset!
$fsText->insertBefore($fsNested, $area);

// selects
$fsSelect = $form->addFieldset()->setLabel('Selects');

$fsSelect->addSelect('selSingleTest')
    ->loadOptions($options)
    ->setLabel('Single select:');

$fsSelect->addSelect('selMultipleTest')
    ->makeMultiple()
    ->setSize(4)
    ->loadOptions($options)
    ->setLabel('Multiple select:');

// checkboxes and radios
$fsCheck = $form->addFieldset()->setLabel('Checkboxes and radios');

$fsCheck->addCheckbox('boxTest')
    ->setContent('check me')
    ->setLabel('Test Checkbox:');

$fsCheck->addRadio('radioTest')
    ->setValue(1)
    ->setContent('select radio #1')
    ->setLabel('Test radio:');

$fsCheck->addRadio('radioTest')
    ->setValue(2)
    ->setContent('select radio #2')
    ->setLabel('(continued)');

$fsCustom = $form->addFieldset()->setLabel('Custom elements');

$fsCustom->addDate('testDate')
    ->setFormat('d-F-Y')
    ->setMaxYear(2001)
    ->setMinYear((int)date('Y'))
    ->setLabel('Today is:');

$fsCustom->addHierselect('testHierselect')
    ->setStyle('width: 20em')
     ->setLabel('Hierarchical select:')
     ->loadOptions(array($main, $secondary))
     ->setSeparator('<br />');

// buttons
$fsButton = $form->addFieldset()->setLabel('Buttons');

$testReset = $fsButton->addReset('testReset')
    ->setLabel('This is a reset button');

$fsButton->addInputButton( 'testInputButton')
    ->setLabel('Click this button')
    ->setAttribute('onclick', "alert('This is a test.');");

$fsButton->addButton('testButton')
    ->setAttribute('onclick', "alert('Almost nothing');")
    ->makeButton()
    ->setContent(
        '<img src="https://pear.php.net/gifs/pear-icon.gif" '.
        'width="32" height="32" alt="pear" />This button does almost nothing'
    );

// submit buttons in nested fieldset
$fsSubmit = $fsButton->addFieldset()->setLabel('These buttons can submit the form');

$fsSubmit->addSubmit('testSubmit')
    ->setLabel('Test Submit');

$fsSubmit->addButton('testSubmitButton')
    ->makeSubmit()
    ->setContent(
        '<img src="https://pear.php.net/gifs/pear-icon.gif" '.
       'width="32" height="32" alt="pear" />This button submits'
    );

$fsSubmit->addImage('testImage')
    ->setURL('https://pear.php.net/gifs/pear-icon.gif');


// outputting form values
if ('POST' === $_SERVER['REQUEST_METHOD']) {
    echo "<pre>\n";
    var_dump($form->getValue());
    echo "</pre>\n<hr />";
    // let's freeze the form and remove the reset button
    $fsButton->removeChild($testReset);
    $form->toggleFrozen(true);
}

$renderer = HTML_QuickForm2_Renderer::createDefault();
$form->render($renderer);
// Output javascript libraries, needed by hierselect
echo $renderer->getJavascriptBuilder()->getLibraries(true, true);
echo $renderer;
?>
  </body>
</html>
