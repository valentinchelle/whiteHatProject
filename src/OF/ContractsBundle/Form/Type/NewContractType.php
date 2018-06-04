<?php
// OFContractsBundle/Form/Type/ProfileFormType.php
namespace OF\ContractsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class NewContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder
        ->add('bounty')
        ->add('name')
        ->add('difficulty',TextType::class, array(
    'required'   => false, 'label' => 'Difficulty'))
    ->add('save',SubmitType::class,array('label' => 'Enregistrer'));
    

    }


  
}