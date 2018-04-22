<?php

namespace OF\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Controller\SecurityController as SecurityController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OF\UserBundle\Entity\User;

/**
 * Description of UserController
 *
 */
class UserController extends SecurityController {
	// Permet d'utiliser l'include du form login.content.html.twig sur toutes les pages
    public function LoginBisAction()
    {
    	//CF \vendor\friendsofsymfony\user-bundle\Controller\SecurityController pour comprendre le crsf et les différents arguments ici.
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Security:login_content.html.twig', array(
            'last_username' => null,
            'error'         => null,
            'csrf_token'    => $csrfToken
        ));
    }
        public function LoginMenuAction()
    {
        //CF \vendor\friendsofsymfony\user-bundle\Controller\SecurityController pour comprendre le crsf et les différents arguments ici.
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Security:login_menu.html.twig', array(
            'last_username' => null,
            'error'         => null,
            'csrf_token'    => $csrfToken
        ));
    }

    public function SideNavAction(){
        $this->getUser()->setSidenav(1- ($this->getUser()->getSidenav()));
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();
        return new Response();
    }

        public function loginAndRegisterAction($page, Request $request)
    {
        /** REGISTER */
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        
        /* LOGIN */
            /** @var $session Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;


        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'formRegister' => $form->createView(),
            'page'=>$page,
        ));
    }
    public function viewProfileAction($id){

        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getManager()->getRepository('OFUserBundle:User')->findOneBy(array('id' => $id));
        if ($user == null){
           throw new NotFoundHttpException("Page Introuvable."); 
        }
        $companies = $user->getCompanies();


        return $this->render('OFUserBundle:Profile:profileExterior.html.twig', array('user'=> $user, 'companies'=>$companies ));
    }


}