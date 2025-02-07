<?php

namespace App\Form;

use App\Entity\Detail;
use App\Entity\Subscription;
use App\Entity\User;
use Symfony\UX\Dropzone\Form\DropzoneType;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [])

            ->add('username', TextType::class)
            ->add('fullname', TextType::class)

            ->add('image', DropzoneType::class, [
                'mapped' => false, // Deconnexion du lien avec l'entité
                
            ])

            // Prêt a ajouter le profil de l'utilisateur
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier votre profil',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
