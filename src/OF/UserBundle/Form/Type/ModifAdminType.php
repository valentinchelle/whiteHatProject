<?php
// OFUserBundle/Form/Type/ProfileFormType.php
namespace OF\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
class ModifAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $permissions = array(
        'Basic User'        => 'ROLE_USER',
        'Publisher'     => 'ROLE_PUBLISHER',
        'Admin'     => 'ROLE_ADMIN',
        'Super Admin' => 'ROLE_SUPER_ADMIN'
    );

        // add your custom field
        $builder
        ->add('nom', TextType::class)
        ->add('nom', TextType::class)
        ->add('prenom',TextType::class)
        ->add('email', null, array(
    'required'   => false))
        ->add('telephone',TextType::class, array(
    'required'   => false))
        ->add('bio',TextType::class, array(
    'required'   => false))
         ->add(
            'roles',
            ChoiceType::class,
            array(
                'multiple' => true,
                'expanded' => false,
                'label'   => 'roles',
                'choices' => $permissions,
            )
        )
        ->add('save', SubmitType::class);
    }

  
}