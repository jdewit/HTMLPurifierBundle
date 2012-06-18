<?php

namespace Exercise\HTMLPurifierBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;

class PurifiedTextareaType extends AbstractType
{
    private $purifierTransformer;

    public function __construct(DataTransformerInterface $purifierTransformer)
    {
        $this->purifierTransformer = $purifierTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->purifierTransformer);
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'purified_textarea';
    }
}
