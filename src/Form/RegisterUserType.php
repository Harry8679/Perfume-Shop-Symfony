<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterUserType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => $this->translator->trans('form.first_name'),
                'label_attr' => ['class' => 'text'],
                'attr' => ['class' => 'input-text', 'title' => 'firstName'],
                'row_attr' => ['class' => 'form-row form-row-wide']
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->translator->trans('form.last_name'),
                'label_attr' => ['class' => 'text'],
                'attr' => ['class' => 'input-text', 'title' => 'lastName'],
                'row_attr' => ['class' => 'form-row form-row-wide']
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('form.email'),
                'label_attr' => ['class' => 'text'],
                'attr' => ['class' => 'input-text', 'title' => 'email'],
                'row_attr' => ['class' => 'form-row form-row-wide']
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => $this->translator->trans('form.password'),
                    'label_attr' => ['class' => 'text'],
                    'attr' => ['class' => 'input-text', 'title' => 'password'],
                    'hash_property_path' => 'password',
                    'row_attr' => ['class' => 'form-row form-row-wide']
                ],
                'second_options' => [
                    'label' => $this->translator->trans('form.password_repeated'),
                    'label_attr' => ['class' => 'text'],
                    'attr' => ['class' => 'input-text', 'title' => 'password'],
                    'row_attr' => ['class' => 'form-row form-row-wide']
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('form.submit')
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
