<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
                'attr' => ['class' => 'input-text', 'title' => 'firstName'],
                'row_attr' => ['class' => 'form-row form-row-wide']
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->translator->trans('form.last_name'),
                'attr' => ['class' => 'input-text', 'title' => 'lastName'],
                'row_attr' => ['class' => 'form-row form-row-wide']
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('form.email'),
                'attr' => ['class' => 'input-text', 'title' => 'email'],
                'row_attr' => ['class' => 'form-row form-row-wide']
            ])
            ->add('password', PasswordType::class, [
                'label' => $this->translator->trans('form.password'),
                'attr' => ['class' => 'input-text', 'title' => 'password'],
                'row_attr' => ['class' => 'form-row form-row-wide']
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
