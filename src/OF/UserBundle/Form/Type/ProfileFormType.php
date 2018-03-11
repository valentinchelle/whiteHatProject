<?php
// OFUserBundle/Form/Type/ProfileFormType.php
namespace OF\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('profilePictureFile')
        ->add('save',SubmitType::class,array('label' => 'Enregistrer'));
        $builder->add('telephone',TextType::class, array(
    'required'   => false, 'label' => 'Téléphone'));
        $builder->add('bio',TextType::class, array(
    'required'   => false, 'label' => 'Biographie'));
    

    }

    public function getParent()
    {

        return 'FOS\UserBundle\Form\Type\ProfileFormType';
        
    }

  
}