# Renderers

## Introduction

The renderer classes are used to convert the form and its
elements into HTML or other structures. They can render 
into any data type, from an HTML string to arrays of 
elements.

## Bundled renderers overview

The bundled renderers are:

- `HTML_QuickForm2_Renderer_Default`  
  Renders a simple HTML form scaffold with all elements.
- `HTML_QuickForm2_Renderer_Array`  
  Creates an array with rendered elements that can be used
  to build the surrounding form structure manually.
- `HTML_QuickForm2_Renderer_Callback`  
  Uses callbacks for all structural form elements to render
  them manually.
- `HTML_QuickForm2_Renderer_Stub`  
  Does not render anything, but can be used to check if
  the form is valid, and for clientside forms.
  
## Creating instances

Each of the renderers has a specialized factory method
to easily create a new instance, like this:

```php
$renderer = HTML_QuickForm2_Renderer::createArray();
``` 

## Creating custom renderers

### Concept: Renderer proxies

The factory method does not return an instance of the
renderer class itself, but a proxy class that implements
the same interface. This is done to ensure that only the
relevant methods are available.

In essence, the proxy class is a wrapper around the
actual renderer. The `HTML_QuickForm2_Renderer_Array`
for example has a lot of public methods, but only those
relevant for the rendering process are available in the
proxy class (like the `toArray()` method in the array
renderer's case).

### Creating a renderer class

A renderer class must extend the `HTML_QuickForm2_Renderer`
class. This already sets the methods that must be implemented
for it to work. Documentation for these methods can be found
in the base renderer class.

To be able to use the renderer, it must be registered
so the factory method can find it. This is done by calling
the following method:

```php
HTML_QuickForm2_Renderer::register('myRendererID', MyRendererClass::class);

$renderer = HTML_QuickForm2_Renderer::create('myRendererID');
```

### Adding a proxy class

By default, the renderer will use the `HTML_QuickForm2_Renderer_Proxy`
class as proxy. If the renderer has public methods (to set rendering
options for example), it is recommended to create a specialized
proxy class that extends the default proxy class. This can implement
the methods to make them available in the IDE code completion.

Here is a simple example:

```php
class MyRendererProxy extends HTML_QuickForm2_Renderer_Proxy
{
    public function setOption(string $name, $value): HTML_QuickForm2_Renderer
    {
        return parent::setOption($name, $value);
    }
}

HTML_QuickForm2_Renderer::register('myRendererID', MyRendererClass::class);

$renderer = HTML_QuickForm2_Renderer::create('myRendererID');

if($renderer instanceof MyRendererProxy) 
{
    $renderer->setOption('myOption', 'myValue');
}
```
