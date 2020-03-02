<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class DefaultController
 *
 * Controller para redireccionado por defecto.
 *
 * @package App\Controller
 */
class DefaultController extends RedirectController
{
    /**
     * Index Controller
     *
     * Redirige en base a ROL al solicitar la ruta /
     *
     * @Route("/", name="index")
     */
    public function index(Request $request, AuthorizationCheckerInterface $authChecker)
    {
      
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
           
            if ($authChecker->isGranted('ROLE_ADMIN')) {
                return $this->redirectAction($request,'admin_useroperator_list');
            } else {
                return $this->redirectAction($request,'private_home');
            }
        } else {
            return $this->redirectAction($request,'security_login');
        }
    }
}
