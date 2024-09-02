<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordUserType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'mapped' => false,
                'label' => $this->translator->trans('update_password_form.actual_password'),
                'attr' => [
                    'placeholder' => $this->translator->trans('update_password_form.actual_password_placeholder')
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 100,
                    ])
                ],
                'mapped' => false,
                'first_options' => [
                    'label' => $this->translator->trans('update_password_form.new_password'),
                    'label_attr' => ['class' => 'text'],
                    'attr' => ['class' => 'input-text', 'title' => 'password', 'placeholder' => 'update_password_form.new_password_placeholder'],
                    'hash_property_path' => 'password',
                    'row_attr' => ['class' => 'form-row form-row-wide']
                ],
                'second_options' => [
                    'label' => $this->translator->trans('update_password_form.confirm_new_password'),
                    'label_attr' => ['class' => 'text'],
                    'attr' => ['class' => 'input-text', 'title' => 'password', 'placeholder' => 'update_password_form.confirm_new_password_placeholder'],
                    'row_attr' => ['class' => 'form-row form-row-wide']
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('update_password_form.submit')
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                // die('Mon évènement marche !');
                $form = $event->getForm();
                $user = $form->getConfig()->getOptions()['data'];
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];
                // 1. Récupérer le mot de passe saisi par l'utilisateur et le comparer avec celui en base
                $isValid = $passwordHasher->isPasswordValid($user, $form->get('actualPassword')->getData());
                // dd($isValid);
                // 2. Si c'est différents renvoie une erreur
                if (!$isValid) {
                    $form->get('actualPassword')->addError(new FormError("Votre mot de passe actuel n'est pas le bon."));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null,
        ]);
    }
}
