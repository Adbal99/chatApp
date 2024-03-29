<?php

namespace App\Controller;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $username = $this->getUser()->getUsername();

        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->getParameter('mercure_secret_key')));
        // lcobucci\jwt library
        $token = $config->builder()
            ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $username)]])
            ->getToken(
                $config->signer(),
                $config->signingKey()
            );

        $response = $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);


        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                $token->toString(),
                (new \DateTime())
                    ->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',
                null,
                false,
                true,
                false,
                'strict'
            )
        );

    

        return $response;
    }
}
