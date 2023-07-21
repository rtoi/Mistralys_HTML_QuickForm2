<?php
/**
 * Example file showcasing the Bootstrap5 form
 * renderer. Can be opened in a browser through
 * a local webserver.
 *
 * @package HTML_QuickForm2
 * @subpackage Examples
 * @see HTML_QuickForm2_Renderer_Bootstrap5
 */

$autoloader = __DIR__.'/../../../vendor/autoload.php';
if(!file_exists($autoloader)) {
    die('Composer autoloader not present. Run <code>composer install</code> first.');
}

require_once $autoloader;

$form = new HTML_QuickForm2('bootstrap5-test');
$renderer = HTML_QuickForm2_Renderer::createBootstrap5();

$form->addHidden('hidden1')->setValue('hidden1-value');
$form->addHidden('hidden2')->setValue('hidden2-value');

$form->addText('text')
    ->setLabel('Text element')
    ->setComment('This is a comment');

$form->addDate('date')
    ->setLabel('Date element')
    ->setComment('The date element automatically uses the inline group mode.');

$form->addCheckbox('checkbox')
    ->setLabel('Checkbox element');

$form->addRadio('radio')
    ->setLabel('Radio element');

$form->addButton('button')
    ->setLabel('Button element')
    ->makeButton()
    ->setComment('Simple button that does not submit the form.');

$form->addFile('file-upload')
    ->addAccept('text/plain')
    ->setLabel('File upload element')
    ->setComment('Text file upload element.');

$form->addSelect('select')
    ->setLabel('Select element')
    ->addOption('Please select...', '');

$form->addPassword('password')
    ->setLabel('Password element');

$form->addInputButton('input-button')
    ->setLabel('Input button');

$fieldset = $form->addFieldset('fieldset')
    ->setLabel('Fieldset element');

$fieldset->addText('fieldset-text')
    ->setLabel('Text element');

$fieldset->addCheckbox('fieldset-checkbox')
    ->setLabel('Checkbox element');

?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>Bootstrap5 Renderer - QuickForm Examples</title>
    </head>
    <body style="padding-top:6rem;">
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Bootstrap5</a>
            </div>
        </nav>

        <main class="container">
            <h1>Renderer example</h1>
            <p>
                This example showcases the Bootstrap5 renderer.
            </p>
            <p>
                NOTE: The Bootstrap libraries are loaded via the CDN,
                and can be added to the page by calling
                <code>$renderer-><?php echo array(HTML_QuickForm2_Renderer_Bootstrap5::class, 'renderCDNIncludes')[1] ?>()</code>.
            </p>
            <hr>
            <?php
            echo $form->render($renderer);
            ?>
        </main>
        <?php
        echo $renderer->renderCDNIncludes();
        ?>
    </body>
</html>
