# ExerciseHTMLPurifierBundle

This bundle integrates [HTMLPurifier][] into Symfony2.

  [HTMLPurifier]: http://htmlpurifier.org/

## Installation

### Submodule Creation

Add HTMLPurifier and this bundle to your `vendor/` directory:

```
$ git submodule add git://github.com/Exercise/HTMLPurifierBundle.git vendor/bundles/Exercise/HTMLPurifierBundle
$ git submodule add git://github.com/ezyang/htmlpurifier.git vendor/htmlpurifier
```

### Class Autoloading

Register "HTMLPurifier" and the "Exercise" namespace prefix in your project's
`autoload.php`:

```
# app/autoload.php

$loader->registerNamespaces(array(
    'Exercise' => __DIR__ . '/../vendor/bundles',
));

$loader->registerPrefixes(array(
    'HTMLPurifier' => __DIR__ . '/../vendor//htmlpurifier/library',
));
```

### Application Kernel

Add HTMLPurifierBundle to the `registerBundles()` method of your application
kernel:

```
# app/AppKernel.php

public function registerBundles()
{
    return array(
        // ...
        new Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),
        // ...
    );
}
```

## Configuration

If you do not explicitly configure this bundle, an HTMLPurifier service will be
defined as `exercise_html_purifier.default`. This behavior is the same as if you
had specified the following configuration:

```
# app/config.yml

exercise_html_purifier:
    default:
        Cache.SerializerPath: '%kernel.cache_dir%/htmlpurifier'
```

The `default` profile is special in that it is used as the configuration for the
`exercise_html_purifier.default` service as well as the base configuration for
other profiles you might define.

```
# app/config.yml

exercise_html_purifier:
    default:
        Cache.SerializerPath: '%kernel.cache_dir%/htmlpurifier'
    custom:
        Core.Encoding: 'ISO-8859-1'
```

In this example, a `exercise_html_purifier.custom` service will also be defined,
which includes both the cache and encoding options. Available configuration
options may be found in HTMLPurifier's [configuration documentation][].

**Note:** If you define a `default` profile but omit `Cache.SerializerPath`, it
will still default to the path above. You can specify a value of `null` for the
option to suppress the default path.

  [configuration documentation]: http://htmlpurifier.org/live/configdoc/plain.html

## Cache Warming ##

When a path is supplied for HTMLPurifier's `Cache.SerializerPath` configuration
option, an error is raised if the directory is not writable. This bundle defines
a cache warmer service that will collect all `Cache.SerializerPath` options and
ensure those directories exist and are writeable.

## Form Data Transformer

This bundles provides a data transformer class for filtering form fields with
HTMLPurifier. Purification is done during the `reverseTransform()` method, which
means that client data will be filtered during binding to the form.

## Form Types
The bundle contains two custom form types (text and textarea) that implement the HTMLPurifier
transformer. See the cookbook entry on <a href="http://symfony.com/doc/current/cookbook/form/create_custom_field_type.html">Custom Form Types</a>.
You can use them like any other form type.

```php
<?php
namespace Acme\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'purified_text')
            ->add('content', 'purified_textarea')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\DemoBundle\Entity\Test'
        ));
    }

    public function getName()
    {
        return 'acme_demo_test';
    }
}
```

Additional documentation on data transformers may be found in the
[Symfony2 documentation][].

  [Symfony2 documentation]: http://symfony.com/doc/2.0/cookbook/form/data_transformers.html

## Twig Filter

This bundles registers a `purify` filter with Twig. Output from this filter is
marked safe for HTML, much like Twig's built-in escapers. The filter may be used
as follows:

``` jinja
{# Filters text's value through the "default" HTMLPurifier service #}
{{ text|purify }}

{# Filters text's value through the "custom" HTMLPurifier service #}
{{ text|purify('custom') }}
```
