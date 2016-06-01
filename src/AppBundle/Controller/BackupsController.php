<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

/**
 * Backups controller.
 *
 * @Route("/backups")
 */
class BackupsController extends Controller
{

    /**
     * @Route("/", name="backups")
     * 
     */
    public function indexAction()
    {
        $finder = new Finder();

        try {
            $finder->files()->in($this->get('kernel')->getRootDir() . '/../backups')->sortByModifiedTime();
        } catch (\Exception $exc) {
            $finder = null;
        }

        return $this->render('backups/index.html.twig', array(
                    'finder' => $finder
        ));
    }

    /**
     * @Route("/{file}", name="backups_download")
     */
    public function downloadAction($file)
    {
        $content = file_get_contents($this->get('kernel')->getRootDir() . '/../backups/' . $file);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'application/gzip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $file);

        $response->setContent($content);
        return $response;
    }

}
