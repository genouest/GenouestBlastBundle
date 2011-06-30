========
Overview
========

This bundle allows you to run BLAST+ from a convenient web interface.

It uses GenouestSchedulerBundle to run BLAST+ jobs on different scheduling system (SGE for example)


How does it work?
-----------------

This bundle contains a ready-to-use BLAST+ form.
The BLAST+ jobs are launched on computation machines (SGE cluster for example) using GenouestSchedulerBundle.
The results can be viewed and downloaded in different formats (html, txt, csv, xml, ...)


.. _installation-label:
Installation
------------
First install GenouestSchedulerBundle which is required by this bundle.

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
    blast_scheduler:
        # The form type class. Change this if you want to use a custom one.
        form_type:            Genouest\Bundle\BlastBundle\Form\BlastType

Usage
-----

