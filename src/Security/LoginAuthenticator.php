<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\AccessLog;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\RouterInterface;


class LoginAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;
    private $em;
    private $urlGenerator;
    private $csrfTokenManager;
    private $authchecker;
    private $router;
    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $em, AuthorizationCheckerInterface $authchecker, RouterInterface $router )
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->em = $em;
        $this->authchecker=$authchecker;
        $this->router=$router;
    }

    public function supports(Request $request)
    {
        return 'security_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $em =  $this->em;
        
        // Load / create our user however you need.
        // You can do this by calling the user provider, or with custom logic here.
        
        $user = $this->em->getRepository(User::class)
        ->findOneBy(['username' =>$credentials['username']]);
        
       
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
     
        return password_verify($credentials['password'],$this->getPassword($credentials));
        //return true;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        $options = [
            'cost' => 12
        ];
        return password_hash($credentials['password'],PASSWORD_BCRYPT,$options);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $em =  $this->em;
        $username = $request->get('_username');

        $accessLog = new AccessLog();
        $accessLog->setUsername($username);
        $accessLog->setAction('User ' . $username . ' has been authenticated successfully');

        $em->persist($accessLog);
        $em->flush();
        $user = $em->getRepository(\App\Entity\User::class)->findOneBy(['username' => $username]);
        if ($this->authchecker->isGranted('ROLE_SUPER_ADMIN')) {
            $response = new RedirectResponse($this->router->generate('admin_useroperator_list'));
        } elseif ($this->authchecker->isGranted('ROLE_ADMIN')) {
            $response = new RedirectResponse($this->router->generate('admin_useroperator_list'));
        } elseif ($this->authchecker->isGranted('ROLE_USER') && $user->isEnabled()) {
            
            $response = new RedirectResponse($this->router->generate('private_home'));
        } else {
            $response = null;
        }

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $em = $this->em;

        $accessLog = new AccessLog();
        $accessLog->setUsername($request->get('_username'));
        $accessLog->setAction('Authentication request failed');
        $em->persist($accessLog);
        $em->flush();

        $referer = $request->headers->get('referer');
        
        
        $request->getSession()->getFlashBag()->add('error', $exception->getMessage());

        return new RedirectResponse($referer);
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('security_login');
    }
}




