<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TemporaryController extends AbstractController
{
    #[Route('/temporary', name: 'app_temporary')]
    public function index(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setEmail("manu.melchior@gmail.com");
        $plainPassword = "281188";
        $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashPassword);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('temporary/index.html.twig', [
            'controller_name' => 'TemporaryController',
        ]);
    }
}
