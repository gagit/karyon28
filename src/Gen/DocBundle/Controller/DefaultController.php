<?php

namespace Gen\DocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DocBundle:Default:index.html.twig', array('name' => $name));
    }
}
