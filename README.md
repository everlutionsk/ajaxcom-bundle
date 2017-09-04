# Everlution AjaxcomBundle

## Installation

### Step 1: Download the Bundle


```console
$ composer require everlutionsk/ajaxcom-bundle
```

### Step 2: Enable the Bundle

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Everlution\AjaxcomBundle\EverlutionAjaxcomBundle(),
        );

        // ...
    }

    // ...
}
```

## Configuration

You don't need to configure anything if you wish to use the flash message templates provided by the bundle.

```yaml
# following are default values
everlution_ajaxcom:
    flash_template: EverlutionAjaxCom::flash_message.html.twig
    flash_block_id: flash_message
```

The bundle works best with Bootstrap 3+ CSS framework.

## Usage

Extend your controller from `Everlution\AjaxcomBundle\Controller\Controller` or use `Everlution\AjaxcomBundle\Controller\AjaxcomTrait` trait with your controller to obtain Ajaxcom functionality.

Every link which you want to call via Ajaxcom must contain `data-ajaxcom` attribute.

Example:

```twig
<a href="{{ path('homepage') }}">Homepage</a> <!-- won't be handled by Ajaxcom -->
<a href="{{ path('remove_user') }}" data-ajaxcom>Remove user</a> <!-- will be handled by Ajaxcom -->
```
The following methods can be combined - eg. you can render multiple blocks and remove multiple blocks and add as many JavaScript callbacks within one request as you wish.

### `protected function render($view, array $parameters = array(), Response $response = null)`

Everlution's Ajaxcom bundle extends standard Symfony's `render()` method so you can integrate Ajaxcom with your project without any further changes within your codebase.

The `render()` method automatically decides if it responding to the Ajax or non-Ajax call so you don't need to handle special scenarios within your application.

The Ajaxcom bundle will handle your Symfony controller's action with Ajax and non-Ajax request automatically so you don't need to write your code twice - the bundle will always call the same action.

Within your standard Symfony controller's action you will have only tiny overhead which will setup the action's behaviour for handling the Ajax requests. The overhead methods are explained in next few sections.

### `renderAjaxBlock(string $id, array $callbacks = [])`

In order to dynamically render only one block on page you need to fit following two conditions:

1. the block which you want to render is enclosed within twig `block`
2. the twig `block` is enclosed within DOM element with `ID` which has same name as the block

#### Example:

Twig:

```twig
<div id="list">
    {% block list %}
        // this is the HTML which will be replaced/removed ...
    {% endblock %}
</div>
```

PHP:

```php
$this->renderAjaxBlock("list");
```

Within your action in the controller then simply call `renderAjaxBlock` where as first argument you need to provide the block ID and as a second argument you can provide an array of JavaScript callbacks which will be called after the block is rendered via Ajaxcom library.

_Callbacks can be added as an associative array where the key is the JavaScript function name and the value is an array of arguments which will be passed as an object to the JavaScript function. See `addCallback(string $function, array $parameters = [])` section for further information._

When your action is called via Ajax request the JSON response for Ajaxcom library will contain information about which block should be re-rendered with which HTML. 

### `removeAjaxBlock(string $id)`

If you want to remove some DOM element dynamically for instance after deleting some row from table you can use `removeAjaxBlock()` method where you will simply provide ID of the element which you want to remove. The ID is simple HTML identifier.

#### Example:

Twig:

```twig
<table>
    <tbody>
        <tr id="row-1"><td>1.</td></tr>
        <tr id="row-2"><td>2.</td></tr>
        <tr id="row-3"><td>3.</td></tr>
    </tbody>
</table>
```

PHP:

```php
$this-removeBlock("row-2");
```

The below code will remove middle row from the table after the action is called.

Result:


```twig
<table>
    <tbody>
        <tr id="row-1"><td>1.</td></tr>
        <!-- the #row-2 has been removed -->
        <tr id="row-3"><td>3.</td></tr>
    </tbody>
</table>
``` 

### `addCallback(string $function, array $parameters = [])`

You can add as many JavaScript callbacks as you wish. First argument of `addCallback()` is name of function which should be called after rendering the HTML, second is array of parameters which will be passed to the function as an object.

Example:

PHP:

```php
$this->addCallback('Table.init', ['some' => 'data', 'other' => ['data', 'etc']]);
```

```javascript
var Table = function() {
    return {
        init: function(data){
            var some = data.some;
            var otherArray = data.other;
            
            // initialize table with provided data
        };
    }
};
```

### `renderModal()`

When you invoke this function within your controller's action, the content which action returns will be rendered within a modal window.

### Flash messages

The flash messages are automatically handled by Ajaxcom bundle and when the request is called via Ajax the flashes which are in the session are rendered automatically.

You only need to include provided twig template somewhere within your twig layout:

```twig
{% include "EverlutionAjaxcomBundle::flash_message.html.twig" %}
```  

When you calling `addFlash()` from your controller, please use `Everlution\AjaxcomBundle\Flash` to provide the flash message type:

```php
$this-addFlash(Everlution\AjaxcomBundle\Flash::SUCCESS, 'Your request has been successfully handled by Ajaxcom bundle');

// you can use following constants:
// Everlution\AjaxcomBundle\Flash::SUCCESS
// Everlution\AjaxcomBundle\Flash::ERROR
// Everlution\AjaxcomBundle\Flash::WARNING
// Everlution\AjaxcomBundle\Flash::INFO
```

# TODO

- add example for rendering modal
- add twig templates for rendering modal
- add complex usage example

