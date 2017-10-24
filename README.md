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

### Step 3: Include Ajaxcom library JavasSript within your TWIG layout

Install `@everlutionsk/ajaxcom-js` via `npm` and include `ajaxcom.min.js` to your TWIG layout:

```twig
<script type="text/javascript" src="{{ asset('build/ajaxcom.min.js') }}"></script>
```

The last thing you need to do is provide some JavaScript handler within your TWIG layout - please follow `@everlutionsk/ajaxcom-js` [documentation](https://github.com/everlutionsk/ajaxcom-js).

## Configuration

You don't need to configure anything if you wish to use the flash message templates provided by the bundle.

```yaml
# all configuration is optional - following values are default
everlution_ajaxcom:
    flash_template: @EverlutionAjaxcom/flash_message.html.twig
    flash_block_id: flash_message
    persistent_class: ajaxcom-persistent
    blocks_to_render: ['content', 'navigation'] # default value is empty array - when you provide this value, AjaxcomBundle will automatically render these blocks within each AJAX request
```

The bundle works best with Bootstrap 3+ CSS framework.

## Usage

Extend your controller from `Everlution\AjaxcomBundle\Controller\Controller` or use `Everlution\AjaxcomBundle\Controller\AjaxcomTrait` trait with your controller to obtain Ajaxcom functionality.

Every link which you want to call via Ajaxcom must contain `data-ajaxcom` attribute. You can change this behaviour in JavaScript Ajaxcom handler.

Example:

```twig
<a href="{{ path('homepage') }}">Homepage</a> <!-- won't be handled by Ajaxcom -->
<a href="{{ path('remove_user') }}" data-ajaxcom>Remove user</a> <!-- will be handled by Ajaxcom -->
```

The following methods can be combined - eg. you can render multiple blocks and remove multiple blocks and add as many JavaScript callbacks within one request as you wish.

### `public function render($view, array $parameters = array(), Response $response = null)`

Everlution's Ajaxcom bundle extends standard Symfony's `render()` method so you can integrate Ajaxcom with your project without any further changes within your codebase.

The `render()` method automatically decides if it responding to the Ajax or non-Ajax call so you don't need to handle special scenarios within your application.

The Ajaxcom bundle will handle your Symfony controller's action with Ajax and non-Ajax request automatically so you don't need to write your code twice - the bundle will always call the same action.

Within your standard Symfony controller's action you will have only tiny overhead which will setup the action's behaviour for handling the Ajax requests. The overhead methods are explained in next few sections.

### `renderAjaxBlock(string $id)`

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

In action of your controller simply call `renderAjaxBlock` where you need to provide the block ID (eg. TWIG block name).

When your action is called via Ajax request the JSON response for Ajaxcom library will contain information about which block should be re-rendered with which HTML.

### `removeAjaxBlock(string $selector)`

If you want to remove some DOM element dynamically for instance after deleting some row from table you can use `removeAjaxBlock()` method where you will simply provide CSS selector of the element which you want to remove.

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
$this->removeBlock("#row-2");

// OR you can use any CSS selector

$this->removeBlock("tr:nth-child(2)");
```

The above code (both examples) will remove middle row from the table after the action is called.

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

### `replaceClass()`

You can easily replace class in any DOM object you want by invoking `replaceClass()` with two arguments - first is CSS selector of your choice and second is class which you want to replace current one with.

### Flash messages

The flash messages are automatically handled by Ajaxcom bundle. When the request is called via Ajax the flashes which are in the session bag are rendered automatically.

You only need to include provided twig template somewhere within your twig layout:

```twig
{% include "@EverlutionAjaxcom/flash_message.html.twig" %}
```  

When you call `addFlash()` from your controller, please use `Everlution\AjaxcomBundle\Flash` to provide the flash message type:

```php
$this->addFlash(Everlution\AjaxcomBundle\Flash::SUCCESS, 'Your request has been successfully handled by Ajaxcom bundle');

// you can use following constants:
// Everlution\AjaxcomBundle\Flash::SUCCESS
// Everlution\AjaxcomBundle\Flash::ERROR
// Everlution\AjaxcomBundle\Flash::WARNING
// Everlution\AjaxcomBundle\Flash::INFO
```

### Sending forms through Ajaxcom

You can simply extend `EverlutionAjaxcom\Form\Type\AjaxcomForm`. In `configureOptions()` then call parent method as follows:

```php
class CustomForm extends AjaxcomForm {
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        
        // configure your options
        $resolver->setDefaults(['data_class' => YourDataClass::class]);
    }
}
```

Or you can add attribute `data-ajaxcom` to your form manually:

```php
class CustomForm extends AbstractType {
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['attr' => ['data-ajaxcom' => '']]);
    }
}
```

# Reusing data sources

In order to reuse data source between for instance multiple tabs you can easily create Twig functions by extending our `BaseDataSource`.

Simply add to your services.yml following statement:

```yaml
    AppBundle\DataProvider\:
        resource: '../../src/AppBundle/DataProvider'
        tags: ['twig.extension']
```

You can specify any folder within your project you want. In this example we have chosen `AppBundle\DataProvider` namespace.

Each class within this namespace which extends `Everlution\AjaxcomBundle\DataSource\BaseDataSource` is scanned for public methods with suffix `Provider` via reflexion and we are creating the simple Twig functions from these methods. Let's see the example:


```php
// AppBundle\DataProvider\Example.php

// simple function which returns static array
public function navigationProvider() {
    return [ 
        // some data... 
    ];
}

// you can use parametrical functions and injected services as well
public function userProfileProvider(int $id) {
    return $this->someService->getData($id);
}
```

After creating such class you can simply call the function within twig:

```twig
{{ dump(navigation()); }} {# will dump static array #}

{% for item in userProfile(2) %}
   {{ dump(item) }}
{% endfor %}
```

# Best practice

If you want to use AjaxcomBundle seamlessly you should copy `@EverlutionAjaxcom\layout_bootstrap_4.html.twig` to your project (eg. AppBundle) and modify it to your needs.

This way the AjaxcomBundle will handle tasks such as replacing JavaScripts, StyleSheets and MetaTags for you.

## Use automated replacement of JS, CSS, Meta and Title

When you are using blocks from `@EverlutionAjaxcom\layout_bootstrap_4.html.twig` you should be all set up.
 
When you decide to set up your layout manually following sections will help you to understand how the automatic replacement works.

### replacing JavaScripts

1. all JavaScript which should be included on every page needs to contain `class='ajaxcom-persistent'` (or anything you have set within configuration of bundle)
2. your main layout which you are extending must contain `{% block javascripts %}{% endblock %}`
3. when you are extending your main layout and you are rewriting `javascripts` block AjaxcomBundle will load scripts from this block automatically for you

### replacing StyleSheets

1. same as JavaScript, all StyleSheets which should be included on every page needs to containe `class='ajaxcom-persistent'` (or anything you have set within configuration of bundle)
2. your main layout which you are extending must contain `{% block stylesheets %}{% endblock %}`
3. when you are extending your main layout and you are rewriting `stylesheets` block AjaxcomBundle will load styles from this block automatically for you

### replacing meta tags and title

1. all meta tags which should be present on every page needs to contain `class='ajaxcom-persistent'` (or anything you have set within configuration of bundle)
2. your main layout which you are extending must contain `{% block metatags %}{% endblock %}`
3. when you are extending your main layout and you are rewriting `metatags` block AjaxcomBundle will load meta tags from this block automatically for you
4. if you want to change `title` of page your layout needs to contain `<title>{% block title %}{% endblock %}</title>` and you need to rewrite `title` block within template where you extending your main template

# TODO

- add complex usage example
