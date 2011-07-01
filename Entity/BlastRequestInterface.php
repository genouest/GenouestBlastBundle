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

namespace Genouest\Bundle\BlastBundle\Entity;

use Genouest\Bundle\SchedulerBundle\Scheduler\SchedulerInterface;

interface BlastRequestInterface
{
    /**
     * Generate a Job object corresponding to this blast request
     *
     * @param Genouest\Bundle\SchedulerBundle\Scheduler\SchedulerInterface A job scheduler instance
     * @return Genouest\Bundle\SchedulerBundle\Entity\Job A job instance
     */
    public function getJob(SchedulerInterface $scheduler);
}

