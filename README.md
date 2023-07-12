# HTML QuickForm 2 - Mistralys fork

This fork focuses on code modernization, performance and 
quality of life improvements. 

## Requirements

- PHP 7.4+ (PHP8 compatible)
- [Composer](https://getcomposer.org)

## Installation

Add as dependency to your `composer.json` file with:

```
composer require mistralys/html_quickform2
```

Also see the [Packagist page](https://packagist.org/packages/mistralys/html_quickform2).

## Additions and improvements

* Chainable element methods as alternative to attribute arrays.
* Element factory class for easy element creation.
* Better code completion support through typed return values.
* Ongoing overall code modernization with strict typing.
* Quality of life improvements overall.
* Removed dependencies on PEAR packages.

Some changes in detail:

* Elements: `set/getRuntimeProperty()` method to store data at runtime
* Default array datasource: `setValues()` method 
* Textarea element: `setRows()` / `setColumns()` methods
* Elements: `makeOptional()` method to remove any required rules
* Elements: `hasErrors()` method to check if an element has errors after validation
* Elements: `getRules()` method to retrieve all rules added to the element
* Elements: `hasRules()` method to check if an element has any rules
* Elements: `addRuleXXX()` shorthand methods, including `addRuleRequired()`.
* Elements: `appendComment()` method to append text to an existing comment.
* Elements: `setComment()` extended to allow `Stringable` values.
* Elements: `isFrozen()` method to check if an element is frozen.
* Containers: `requireElementById()` with non-null return value.
* Text-based elements: `addFilterTrim()` method. 
* Select element: `prependOption()` method to insert an element at the top.
* Select element: Support for selects with a custom OptGroup class.
* Select element: OptGroups `getLabel()` method.
* Select element: `countOptions()` method with recursive capability.
* Select Element: Added `makeMultiple()` and `isMultiple()`.
* Select Element: Added `setSize()` and `getSize()`.
* Select Element: Added `getSelectedOption()`.
* Date element: Setter and getter methods for options.
* Input elements: Added `setLabel()` method where relevant.
* Checkbox element: Added `setChecked()` method.
* Image element: Added `setURL()` method.
* Button Element: Added `makeSubmit()` and `makeButton()`.
* Form: Added `getMethod()`.
* Form: Added `makeMultiPart()` and `isMultiPart()`.
* Rules: The callback rule now has a method `getCallback()` to retrieve the configured callback.
* Rules: Added static `setDefaultMessage()` to the required rule.

## Performance tweaks

* Container `getElementById()` method.
* Auto-generated element IDs.

## Element ID generation

The element ID generation mechanism has been modified, so it is no longer possible
to rely on a specific naming scheme to predict the automatic element IDs. In practice,
this was impractical at best anyway, and the new system has big performance gains. 

# Documentation

See the main branch for details and documentation: https://github.com/pear/HTML_QuickForm2
