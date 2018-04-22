<?php

namespace OF\ContractsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OF\ContractsBundle\Entity\Contract;
use OF\ContractsBundle\Entity\Company;
use OF\ContractsBundle\Form\Type\NewContractType;
use OF\ContractsBundle\Form\Type\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EnterpriseController extends Controller
{
    public function indexAction()
    {

        return $this->render('OFContractsBundle:Enterprise:index.html.twig');
    }
    public function newContractAction(Request $request,  $idCompany){
        $em = $this->getDoctrine()->getManager();
        $contract = new Contract();

        $company = $this->getDoctrine()->getManager()->getRepository('OFContractsBundle:Company')->findOneBy(array('id' => $idCompany));
        if($company==null){
            throw $this->createNotFoundException('The company does not exist.');
        }
        $form   = $this->get('form.factory')->create(NewContractType::class, $contract);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($contract);
            $em->flush();
        }
        return $this->render('OFContractsBundle:Enterprise:newContract.html.twig', array('form'=>$form->createView(), 'company'=>$company));
    }

    public function createContractAction(Request $request){
        if($request->isXMLHttpRequest()){
            
            $em = $this->getDoctrine()->getManager();

            $company_id = $request->get(('company'));
            $company = $this->getDoctrine()->getManager()->getRepository('OFContractsBundle:Company')->findOneBy(array('id' => $company_id));
            if($company==null){
                throw $this->createNotFoundException('The company does not exist.');
            }

            $value = $request->get(('bounty'));
            $difficulty = $request->get(('difficulty'));
            $contract = new Contract();
            $contract->setCompany($company);
            $company->addContract($contract);
            $contract->setBounty($value);
            $contract->setDifficulty($difficulty);
            $contract->setAddress($request->get(('address')));
            $em->persist($contract);
            $em->persist($company);
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

    public function newCompanyAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $company = new Company();
        $form   = $this->get('form.factory')->create(CompanyType::class, $company);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $company->setOwner($this->getUser());
            $company->setDate(new \Datetime());
            $this->getUser()->addCompany($company);
           
            // For the picture :
            $company->preUploadLogoPicture();
            $company->uploadLogoPicture();

            // We are done, so we have to persist.
            $em->persist($company);
            $em->persist($this->getUser());

            $em->flush();
        }
        return $this->render('OFContractsBundle:Enterprise:companyForm.html.twig', array('form'=>$form->createView()));
    }


}
