<?php

namespace App\Form\Type\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', EmailType::class, [
                'label' => 'form_security.email.label',
                'attr' => ['autocomplete' => 'username'],
                'constraints' => [
                    new NotBlank(
                        message: 'form_security.email.error.required'
                    ),
                    new Email(
                        message: 'form_security.email.error.invalid'
                    ),
                ],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'form_security.password.label',
                'attr' => ['autocomplete' => 'current-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'form_security.password.error.required'
                    ),
                ],
                'mapped' => false,
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'form_security.remember_me.label',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // This form is only rendered: the submission is handled by the form_login authenticator,
            // which checks the "_csrf_token" field (token id "authenticate") added in login.html.twig.
            'csrf_protection' => false,
            'translation_domain' => 'form_security',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'account_security';
    }
}
