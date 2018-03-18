<?php

namespace OF\ContractsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OF\ContractsBundle\Entity\Contract;
use OF\ContractsBundle\Form\Type\NewContractType;
use Symfony\Component\HttpFoundation\Request;


class ExpertController extends Controller
{
    public function indexAction()
    {

        return $this->render('OFContractsBundle:Expert:index.html.twig');
    }
    public function listContractsAction(Request $request){
        $listQuery = $this->getDoctrine()->getManager()->getRepository('OFContractsBundle:Contract')->createQueryBuilder('a')->getQuery();
        //on rajoute la pagination
    	$paginator  = $this->get('knp_paginator');
    	$pagination = $paginator->paginate(
        $listQuery, /* query NOT result */
        $request->query->getInt('page', 1)/*page number*/,
        8/*limit per page*/
    	);

        return $this->render('OFContractsBundle:Expert:list.html.twig', array('pagination'=> $pagination));
    }

    public function  viewContractAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $contract = $this->getDoctrine()->getManager()->getRepository('OFContractsBundle:Contract')->findOneBy(array('id' => $id));
        if ($contract == NULL){
        	throw $this->createNotFoundException('Contract not found.');
        }else{         
            return $this->render('OFContractsBundle:Expert:view.html.twig', array('contract'=> $contract));
        }

    }
}
