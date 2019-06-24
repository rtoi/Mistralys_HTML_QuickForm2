# HTML_QuickForm2 - Mistralys fork

This fork focuses on quality of life improvements, as well as performance enhancements when working 
with large forms, or many parallel forms. 

Additions:

  * Elements: set/getRuntimeProperty() method to store data at runtime
  * Default array datasource: setValues() method 
  * Textarea element: setRows() / setColumns() methods
  * Elements: makeOptional() method to remove any required rules
  * Elements: hasErrors() method to check if an element has errors after validation
  * Elements: getRules() method to retrieve all rules added to the element
  * Elements: hasRules() method to check if an element has any rules 
  * Rules: The callback rule now has a method getCallback() to retrieve the configured callback
  * Text-based elements: addFilterTrim() method 
  * Select element: prependOption() method to insert an element at the top
  * Select optgroups: getLabel() method
  * Select element and optgroups: countOptions() method with recursive capability

Performance tweaks:

  * Container getElementById() method 
  * Auto-generated element IDs

**NOTE**: Element IDs are generated a bit differently in this fork. If your clientside form 
handling does not rely on the naming scheme of the generated IDs, it will not be an issue.


# Documentation

See the main branch for details and documentation: https://github.com/pear/HTML_QuickForm2
