<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use function in_array;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('admin', CheckboxType::class, ['required' => false,])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'constraints' => [
                    new Callback([$this, 'validatePassword']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function validatePassword(string $password, ExecutionContextInterface $context): void
    {
        $user = $context->getRoot()->getData(); // récupère l'objet User

        if (!$user instanceof User) {
            return; // s'assurer que l'on traite bien un objet User
        }

        $name = mb_strtolower($user->getName());
        $email = mb_strtolower($user->getEmail());
        $password = mb_strtolower($password);

        if (in_array($password, [$email, $name], true)) {
            $context
                ->buildViolation('Le mot de passe ne peut pas être identique à votre nom ou votre adresse e-mail.')
                ->atPath('password')
                ->addViolation();
        }
    }
}
