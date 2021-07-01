<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/admin_register",name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passEncoder)
    {
        //Cette route a pour fonction de créer un nouvel Utilisateur pour notre connexion
        //Pourcela, nous allons créer un formulaire interne de création d'utilisateur
        //Pour enregistrer notre nouvel utilisatzur dans la BDD, nous avons besoin de l'Entity Manager
        $entityManager = $this->getDoctrine()->getManager();
        //Nous créons notre formulaire interne
        $userForm = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => "Nom de l'utilisateur",
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
            ])
            ->add('matricle', TextType::class, [
                'label' => 'Matricule de l\'utilisateur'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Role : USER' => 'ROLE_USER',
                    'Role : ADMIN' => 'ROLE_ADMIN',
                    'Role : SUPERADMIN' => 'ROLE_SUPERADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Création',
                'attr' => [
                    'style' => 'margin-top : 5px',
                    'class' => 'w3-button w3-green w3-margin-bottom',
                ]
            ])
            ->getForm();
        //Nous traitons les données reçues au sein de notre formulaire
        $userForm->handleRequest($request);
        if ($request->isMethod('post') && $userForm->isValid()) {
            $data = $userForm->getData();
            $user = new Admin;
            $user->setUsername($data['username']);
            $user->setMatricule($data['matricle']);
            $user->setPassword($passEncoder->encodePassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('app_login'));
        }
        //Si le formulaire n'est pas validé, nous chargeons le template générique de formulaire
        return $this->render('index/dataform.html.twig', [
            "dataForm" => $userForm->createView(),
            "formName" => 'Inscription Utilisateur (Admin)'
        ]);
    }

    /**
     * @Route("/register",name="user_register")
     */
    public function userRegister(Request $request, UserPasswordEncoderInterface $passEncoder)
    {
        //Cette route a pour fonction de créer un nouvel Utilisateur pour notre connexion
        //Pourcela, nous allons créer un formulaire interne de création d'utilisateur
        //Pour enregistrer notre nouvel utilisatzur dans la BDD, nous avons besoin de l'Entity Manager
        $entityManager = $this->getDoctrine()->getManager();
        //Nous créons notre formulaire interne
        $userForm = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => "Nom de l'utilisateur",
            ])
            ->add('address', TextareaType::class, [
                'label' => "Adresse de l\'utilisateur",
            ])
            ->add('telephone', TextType::class, [
                'label' => "Téléphone de l\'utilisateur",
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => "Méthode de paiement",
                'choices' => [
                    'Carte bancaire' => 'carte',
                    'Chèque' => 'cheque',
                ]
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Création',
                'attr' => [
                    'style' => 'margin-top : 5px',
                    'class' => 'w3-button w3-green w3-margin-bottom',
                ]
            ])
            ->getForm();
        //Nous traitons les données reçues au sein de notre formulaire
        $userForm->handleRequest($request);
        if ($request->isMethod('post') && $userForm->isValid()) {
            $data = $userForm->getData();
            $user = new Customer;
            $user->setUsername($data['username']);
            $user->setAddress($data['address']);
            $user->setTelephone($data['telephone']);
            $user->setPaymentMethod($data['paymentMethod']);
            $user->setPassword($passEncoder->encodePassword($user, $data['password']));
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('app_login'));
        }
        //Si le formulaire n'est pas validé, nous chargeons le template générique de formulaire
        return $this->render('index/dataform.html.twig', [
            "dataForm" => $userForm->createView(),
            "formName" => 'Inscription Utilisateur (Customer)'
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
