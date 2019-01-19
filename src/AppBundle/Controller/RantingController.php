<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ranting controller.
 *
 * @Route("/ranting",)
 */
class RantingController extends Controller
{

  /**
   * @Route("/", name="ranting_show")
   * @Template("ranting/index.html.twig")
   */
  public function showAction()
  {
    return array();
  }

  /**
   * @Route("/edit", name="ranting_edit")
   * @Template("ranting/edit.html.twig")
   */
  public function editAction(Request $request)
  {
    // Output file.
    $filepath = $this->container->getParameter('kernel.root_dir') .
        '/Resources/views/ranting/ranting.html.twig';
    // Dummy object used in the form.
    $file = new \ArrayObject();

    // Check that the output file exists.
    if (file_exists($filepath)) {
      // Load the file content.
      $file->content = file_get_contents($filepath);
    } else {
      //TODO: revise
      // The file is missing, load the file located in the Bundle instead.
      $file->content = file_get_contents(
          $this->container->get('kernel')->locateResource(
              '/Resources/views/ranting/menu.html.twig')
      );
    }

    // Declare the form.
    $form = $this->createFormBuilder($file)
        // A textarea for the file content.
        ->add('content', 'textarea', array(
          'attr' => array(
            'rows' => 16,
            'class' => 'tinymce',
          )
        ))
        ->add('submit', 'submit', array(
          'label' => 'Save'
        ))
        ->getForm();

    $form->handleRequest($request);
    if ($form->isValid()) {
      // Save the file in app/Resources/...
      file_put_contents($filepath, $file->content);

      // Clear cache (otherwise the changes won't be applied)
      // Be patient, it can take about 15 seconds.
      $console = 'php ' . $this->container->getParameter('kernel.root_dir') .
          '/console';

      exec($console . ' cache:clear --env=' . $this->getParameter('kernel.environment'));
      exec($console . ' cache:warmup --env=' . $this->getParameter('kernel.environment'));

      $this->get('session')->getFlashBag()->add('notice', 'Content saved succesfully.');

      return $this->redirect($this->generateUrl('ranting_show'));
    }
    return array(
      'form' => $form->createView(),
    );
  }

}
