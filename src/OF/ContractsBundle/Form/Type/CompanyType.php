<?php
// OFContractsBundle/Form/Type/ProfileFormType.php
namespace OF\ContractsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder
        ->add('name')
        ->add('save',SubmitType::class,array('label' => 'Enregistrer'));
    

    }


  
}