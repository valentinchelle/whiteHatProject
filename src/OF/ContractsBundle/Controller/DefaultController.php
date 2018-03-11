<?php

namespace OF\ContractsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OFContractsBundle:Default:index.html.twig');
    }
}
