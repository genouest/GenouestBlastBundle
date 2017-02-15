Overview
========

This bundle allows you to run BLAST+ from a convenient web interface.

It uses GenouestSchedulerBundle to run BLAST+ jobs on different scheduling system (SGE for example)

How does it work?
-----------------

This bundle contains a ready-to-use BLAST+ form (tested with BLAST+ 2.2.25+).
The BLAST+ jobs are launched on computation machines (SGE cluster for example) using GenouestSchedulerBundle.
The results can be viewed and downloaded in different formats (html, txt, csv, xml, ...)
The CSRF protection is disable by default on this blast form, as there is no particular security risk with it.


Installation
------------

You need to have BLAST+ installed and properly configured (download from ftp://ftp.ncbi.nih.gov/blast/executables/blast+/LATEST/).
BLAST+ binaries should be in the PATH of the computing machines.

Install and configure GenouestSchedulerBundle and GenouestBioinfoBundle which are required by this bundle.

Checkout a copy of the bundle code:

.. code-block:: bash

    git submodule add git@github.com:genouest/GenouestBlastBundle.git vendor/bundles/Genouest/Bundle/BlastBundle

Then register the bundle with your kernel:

.. code-block:: php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Genouest\Bundle\BlastBundle\GenouestBlastBundle(),
        // ...
    );

Make sure that you also register the namespaces with the autoloader:

.. code-block:: php

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Genouest\\Bundle' => __DIR__.'/../vendor/bundles',
        // ...
    ));

Import the routes defined in the bundle. Make sure to add these lines AFTER the GenouestSchedulerBundle routes import:

.. code-block:: yaml

    // app/config/routing.yml
    // ...
    _blast:
        resource: "@GenouestBlastBundle/Controller/BlastController.php"
        prefix: /blast
        type: annotation
    // ...

Publish the assets in the web dir:

.. code-block:: bash

    app/console assets:install --symlink web/

Configuration
-------------

Don't forget to configure properly the GenouestSchedulerBundle too.
The following configuration keys are available (with their default values):

.. code-block:: yaml

    # app/config/config.yml
    genouest_blast:
        # Title of the form. Optional
        title:         Blast server (v2.2.26+)

        # The form type class. Change this if you want to use a custom one.
        form_type:       Genouest\Bundle\BlastBundle\Form\BlastType

        # The blast request object. Change this if you want to use a custom one (it should implement Genouest\Bundle\BlastBundle\Entity\BlastRequestInterface).
        request_class:   Genouest\Bundle\BlastBundle\Entity\BlastRequest

        # The names given to jobs (in particular for drmaa jobs)
        scheduler_name: blast

        # The path to the CDD_DELTA databank
        # Downloaded from ftp://ftp.ebi.ac.uk/pub/databases/ncbi/blast/db/cdd_delta.tar.gz and unzipped in /some/path/to/cdd_delta/
        # Deltablast will be unavailable if this option is not populated
        cdd_delta_path:  /some/path/to/cdd_delta/cdd_delta

        # If you need you can add a command that will be executed just before running the blast job.
        # It is a good place to add the blast binaries to the path
        pre_command: "export PATH=/some/blast/bin:$PATH"

        # Define how to retrieve the databank list. Choose only one of the three available method
        db_provider:
            # Use a BioMAJ server. Requires the GenouestBiomajBundle installed and configured.
            biomaj:
                type:
                    nucleic:      ['nucleic', 'foo'] # List of biomaj bank types for nucleic banks
                    proteic:      ['proteic', 'bar'] # List of biomaj bank types for proteic banks
                format:     blast # Biomaj bank format
                cleanup:    true # Should the bank names be cleaned up
                prefix:     "/db/" # Use the BiomajPrefix constraint for performance reason. Delete this line to use the standard Biomaj constraint.
                default:
                    nucleic:      '/db/mybank/*/blast/some/file' # Default nucleic bank. Wildchar (*) is allowed (for the bank version in particular).
                    proteic:      '/db/myproteicbank/*/blast/some/file' # Default proteic bank. Wildchar (*) is allowed (for the bank version in particular).
            # Specify the list of banks directly in the config file
            list:
                nucleic:      {"/db/some/nucl/db" : "My cool nucleic db!", "/db/some/other/nucl/db" : "Another nucleic db!"}
                proteic:      {"/db/some/prot/db" : "My cool proteic db!", "/db/some/other/prot/db" : "Another proteic db!"}
            # Use a PHP class implementing Genouest\Bundle\BlastBundle\DbProvider\CallbackDbProvider. See DummyDbProvider class for an example.
            callback:        Genouest\Bundle\BlastBundle\DbProvider\DummyDbProvider

Customization
-------------

Customizing the Blast+ command
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The blast command line is generated using a twig template. To customize it, you only need to
override the 'GenouestBlastBundle:Blast:command.txt.twig' template.
Be careful not to add unwanted line breaks that would break the bash script.

Customizing the form
~~~~~~~~~~~~~~~~~~~~

It is possible to customize the way this form work. In the configuration, you can change the form type class
to a custom one. This will allow you to change the fields displayed in the form.
There is also a possibility to replace the default BlastRequest entity: with this you can for example change
the constraints applied to each parameters, or their default values.

You can customize the template displaying the form itself: GenouestBlastBundle:Blast:index.html.twig

Finally, this bundle brings a specific result page for the scheduler bundle. You can override it: GenouestBlastBundle:Blast:results.html.twig
