<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderers
 * @see \HTML\QuickForm2\Renderer\Bootstrap5\ElementTemplates
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Renderer\Bootstrap5;

/**
 * Holds HTML snippets for the available element types
 * used in the {@see \HTML_QuickForm2_Renderer_Bootstrap5}
 * renderer.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderers
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ElementTemplates
{
    public const TEMPLATE_CDN_INCLUDES = <<<'HTML'
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
HTML;

    public const TEMPLATE_QUICKFORM = <<<'HTML'
<div class="quickform">
    {errors}
    <form{attributes}>
        <div class="hiddens">
            {hidden}
        </div>
        {content}
        {submits}
    </form>
    <qf:reqnote>
        <div class="reqnote">{reqnote}</div>
    </qf:reqnote>
</div>
HTML;
    public const TEMPLATE_BASE_ELEMENT = <<<'HTML'
<div class="mb-3">
    <qf:label>
        <label for="{id}" class="form-label">
            {label}
            <qf:required>
                <span class="required">*</span>
            </qf:required>
        </label>
    </qf:label>
    {element}
    <div class="form-text">{comment}</div>
    <qf:error>
        <span class="error">{error}<br /></span>
    </qf:error>
</div>
HTML;
    public const TEMPLATE_DATE = <<<'HTML'
<div class="mb-3 form-group {class}" id="{id}">
    <qf:label>
        <label class="form-label">
            {label}
            <qf:required><span class="required">*</span></qf:required>
        </label>
    </qf:label>
    <div class="input-group">
        {content}
    </div>
    <div class="form-text">{comment}</div>
    <qf:error>
        <span class="error">{error}</span>
    </qf:error>
</div>
HTML;
    public const TEMPLATE_CHECKABLE = <<<'HTML'
<div class="mb-3 form-check">
    {element}
    <qf:label>
        <label for="{id}" class="form-check-label">
            {label}
            <qf:required>
                <span class="required">*</span>
            </qf:required>
        </label>
    </qf:label>
    <div class="form-text">{comment}</div>
    <qf:error>
        <span class="error">{error}</span>
    </qf:error>
</div>
HTML;
}
