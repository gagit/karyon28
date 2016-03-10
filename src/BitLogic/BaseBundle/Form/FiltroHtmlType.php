<?php

namespace BitLogic\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FiltroHtmlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('pagenum', 'number',array(
                //'mapped'=>false
            ))
            ->add('pagenum', 'number',array(
                'mapped'=>false
            ))
            ->add('pagesize', 'number',array(
                'mapped'=>false
            ))
            ->add('sortdatafield', 'text',array(
                'mapped'=>false
            ))
            ->add('sortorder', 'text',array(
                'mapped'=>false
            ))
            ->add('btnBuscar', 'submit',array(   
                'attr'=> array(
                         'class'=>'btn btn-default glyphicon glyphicon-search',
                         'data-toggle' => 'tooltip',
                         'data-placement'=> 'top',
                         'title'=> 'Buscar',
                          ),
                  'label'=>' ')
                  )
            ->setMethod('POST');

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BitLogic\BaseBundle\Grilla\Grilla',
            'csrf_protection' => false,
        ]);
    }

    public function getName()
    {
        return 'filtroHtml';
    }
}