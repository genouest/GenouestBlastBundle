<?php

/*
 * Copyright 2011 Anthony Bretaudeau <abretaud@irisa.fr>
 *
 * Licensed under the CeCILL License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt
 *
 */
namespace Genouest\Bundle\BlastBundle\Tests;

require_once(__DIR__ . "/../../../../../../app/AppKernel.php");

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $_container;

    public function __construct()
    {
        $kernel = new \AppKernel("test", true);
        $kernel->boot();
        $this->_container = $kernel->getContainer();
        parent::__construct();
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }
}
