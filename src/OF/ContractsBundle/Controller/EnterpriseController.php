<?php

namespace OF\ContractsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OF\ContractsBundle\Entity\Contract;
use OF\ContractsBundle\Form\Type\NewContractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function createContractAction(Request $request){
        if($request->isXMLHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $value = $request->get(('bounty'));
            $difficulty = $request->get(('difficulty'));
            $contract = new Contract();
            $contract->setBounty($value);
            $contract->setDifficulty($difficulty);
            $contract->setAddress($request->get(('address')));
            $em->persist($contract);
            $em->flush();
            return new Response('Contract saved');
        }else{
            return new Response('Contract not saved');
        }

    }

    public function panelAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        return $this->render('OFContractsBundle:Enterprise:panel.html.twig');
    }
}
