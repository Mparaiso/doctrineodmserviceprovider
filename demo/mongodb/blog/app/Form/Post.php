<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Post extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->add('title')->add('body', 'textarea');
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return "post";
    }

}
