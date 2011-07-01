========
Overview
========

This bundle allows you to run BLAST+ from a convenient web interface.

It uses GenouestSchedulerBundle to run BLAST+ jobs on different scheduling system (SGE for example)


How does it work?
-----------------

This bundle contains a ready-to-use BLAST+ form (tested with blast+ 2.2.25+).
The BLAST+ jobs are launched on computation machines (SGE cluster for example) using GenouestSchedulerBundle.
The results can be viewed and downloaded in different formats (html, txt, csv, xml, ...)
The CSRF protection is disable by default on this blast form, as there is no particular security risk with it.


.. _installation-label:
Installation
------------

You need to have blast+ installed and properly configured. Blast+ binaries should be in the PATH of the computing machines.

Install GenouestSchedulerBundle which is required by this bundle.

Checkout a copy of the bundle code::

    git submodule add gitolite@chili.genouest.org:sf2-blastbundle vendor/bundles/Genouest/Bundle/BlastBundle
    
Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Genouest\Bundle\BlastBundle\GenouestBlastBundle(),
        // ...
    );

Make sure that you also register the namespaces with the autoloader::

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Genouest\\Bundle' => __DIR__.'/../vendor/bundles',
        // ...
    ));

Finally, import the routes defined in the bundle. Make sure to add these lines BEFORE the GenouestSchedulerBundle routes import::

    // app/config/routing.yml
    // ...
    _blast:
        resource: "@GenouestBlastBundle/Controller/BlastController.php"
        type: annotation
    // ...


Configuration
-------------

Don't forget to configure properly the GenouestSchedulerBundle too.
The following configuration keys are available (with their default values)::

    # app/config/config.yml
    genouest_blast:
        # The form type class. Change this if you want to use a custom one.
        form_type:       Genouest\Bundle\BlastBundle\Form\BlastType
        
        # The blast request object. Change this if you want to use a custom one (it should implement Genouest\Bundle\BlastBundle\Entity\BlastRequestInterface).
        request_class:   Genouest\Bundle\BlastBundle\Entity\BlastRequest

Customization
-------------

Customizing the Blast+ command
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The blast command line is generated using a twig template. To customize it, you only need to
override the 'GenouestBlastBundle:Blast:command.txt.twig' template.

Customizing the form
~~~~~~~~~~~~~~~~~~~~

It is possible to customize the way this form work. In the configuration, you can change the form type class
to a custom one. This will allow you to change the fields displayed in the form.
There is also a possibility to replace the default BlastRequest entity: with this you can for example change
the constraints applied to each parameters, or their default values.

You can customize the template displaying the form itself: GenouestBlastBundle:Blast:index.html.twig

Finally, this bundle brings a specific result page for the scheduler bundle. You can override it: GenouestBlastBundle:Blast:results.html.twig
