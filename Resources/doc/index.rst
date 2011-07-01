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

Usage
-----

Cutomizing the Blast+ command
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Customizing the form
~~~~~~~~~~~~~~~~~~~~
