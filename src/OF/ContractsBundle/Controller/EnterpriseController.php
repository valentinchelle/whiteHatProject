<?php

namespace OF\ContractsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OF\ContractsBundle\Entity\Contract;
use OF\ContractsBundle\Form\Type\NewContractType;
use Symfony\Component\HttpFoundation\Request;


class EnterpriseController extends Controller
{
    public function indexAction()
    {

        return $this->render('OFContractsBundle:Enterprise:index.html.twig');
    }
    public function newContractAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $contract = new Contract();
        $form   = $this->get('form.factory')->create(NewContractType::class, $contract);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($contract);
            $em->flush();
        }
        return $this->render('OFContractsBundle:Enterprise:newContract.html.twig', array('form'=>$form->createView()));
    }
}
