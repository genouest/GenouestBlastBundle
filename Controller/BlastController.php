<?php

namespace Genouest\Bundle\BlastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Genouest\Bundle\BlastBundle\Form\BlastType;
use Genouest\Bundle\BlastBundle\Entity\BlastRequest;
use Genouest\Bundle\SchedulerBundle\Entity\Job;

class BlastController extends Controller
{
    /**
     * Main blast form
     *
     * @Route("/", name = "_welcome")
     * @Template()
     */
    public function indexAction()
    {
        $blastRequest = new BlastRequest();
        
        $form = $this->get('form.factory')->create(new BlastType());
        
        $form->setData($blastRequest); // Default values
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $job = $blastRequest->getJob($this->get('scheduler.scheduler'), $this->generateUrl('_welcome', array(), true));
                
                return $this->forward('SchedulerBundle:Scheduler:launchJob', array('job' => $job));
            }

        }

        return $this->render('BlastBundle:Blast:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Show results
     *
     * @Route("/job/results/{uid}", name = "_job_results")
     * @Template()
     */
    public function jobResultsAction($uid) {
        // Load job from db
        $scheduler = $this->get('scheduler.scheduler');
        $jobRepo = $this->get('job.repository');
        $job = $jobRepo->find($uid);
        
        // Check that job is valid
        if (!$job || !$job->isLaunched())
            return $this->render('SchedulerBundle:Scheduler:error.html.twig', array('job' => $job, 'uid' => $uid, 'error' => 'Job '.$uid.' is not available.'));
        
        // Finished?
        if (!$scheduler->isFinished($job)) {
            return new RedirectResponse($this->generateUrl('_job_status', array('uid' => $job->getJobUid())));
        }
        
        $textStatus = $scheduler->getStatusAsText($scheduler->getStatus($job));
        $resultUrl = $scheduler->getResultUrl($job);
        
        // We need to check if the results are there, and to display only the first x lines
        $htmlFile = '';
        foreach ($job->getResultFiles() as $file) {
            if ($file->getDisplayName() == 'HTML blast output')
                $htmlFile = $scheduler->getWorkDir($job).$file->getFsName();
        }
        
        $blastCrashed = false;
        $truncatedPreview = false;
        $previewResults = '';
        if (empty($htmlFile) || !file_exists($htmlFile) || (filesize($htmlFile) <= 0))
            $blastCrashed = true;
        else {
            $lengthToShow = 75000;
            $resExtract = file_get_contents($htmlFile, false, NULL, 0, $lengthToShow);
            $startPre = strpos($resExtract, "<PRE>");
            $endPre = strpos($resExtract, "</BOD");
            if ($endPre > $startPre)
                $previewResults = substr($resExtract, $startPre, $endPre);
            else {
                $previewResults = substr($resExtract, $startPre);
                $previewResults = $this->closeTags($previewResults);
                $truncatedPreview = true;
            }
        }
        
        return $this->render('SchedulerBundle:Scheduler:results.html.twig', array('job' => $job,
            'status' => $textStatus,
            'resultUrl' => $resultUrl,
            'blastCrashed' => $blastCrashed,
            'previewResults' => $previewResults,
            'truncatedPreview' => $truncatedPreview,
            ));
    }
    
    // TODO
    protected function showJobsAction()
    {
        //echo $this->get('job.repository')->getJobsForUser("");
    }



    /**
     * Close any opened tags in an html snippet. Useful when you want to only show the x first bytes of an html file.
     */
    function closeTags($html) {
        # remove ending incomplete tage (like <a href="blabl)
        $lastOpened = strrpos($html, "<");
        $lastClosed = strrpos($html, ">");
        if ($lastOpened > $lastClosed)
            $html = substr($html, 0, $lastOpened);

        # put all opened tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];   #put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);

        # all tags are closed
        if (count($closedtags) == $len_opened)
            return $html;

        $openedtags = array_reverse($openedtags);

        # close tags
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags))
                $html .= '</'.$openedtags[$i].'>';
            else
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
        return $html;
    }
}
