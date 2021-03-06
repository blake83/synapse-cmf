<?php

namespace Synapse\Cmf\Bundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType as SymfonyTextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Synapse\Cmf\Bundle\Form\Type\Media\ImageChoiceType;
use Synapse\Cmf\Bundle\Form\Type\Theme\ComponentDataType;

/**
 * Text component form type.
 */
class TextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ComponentDataType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'html' => array('enabled' => false),
            'headline' => array('enabled' => false),
            'read_more' => array('enabled' => false),
            'images' => array(),
            'ckeditor_config' => array(),
        ));
        $resolver->setAllowedTypes('html', 'array');
        $resolver->setAllowedTypes('headline', 'array');
        $resolver->setAllowedTypes('read_more', array('array', 'null'));
        $resolver->setAllowedTypes('images', array('array', 'null'));
        $resolver->setAllowedTypes('ckeditor_config', 'array');
    }

    /**
     * Text component form prototype definition.
     *
     * @see FormTypeInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // title
        $builder->add('title', SymfonyTextType::class, array(
            'required' => false
        ));

        // headline (on demand)
        if (!empty($options['headline']['enabled'])) {
            $builder->add('headline', TextareaType::class, array(
                'required' => false
            ));
        }

        // text (rich editor or not)
        $builder->add('text', TextareaType::class, array(
            'attr' => array(
                'class' => empty($options['html']['enabled'])
                    ? 'synapse-simple-editor'
                    : 'synapse-rich-editor',
                'data-editor-config' => json_encode($options['ckeditor_config']),
            ),
        ));

        // image select (on demand)
        if (!empty($options['images'])) {
            $builder->add('images', ImageChoiceType::class, array(
                'image_formats' => $options['images']['format'],
                'required' => false,
                'expanded' => false,
                'multiple' => !empty($options['images']['multiple']),
            ));
        }

        // "Read more" link (on demand)
        if (!empty($options['read_more']['enabled'])) {
            $builder
                ->add('link', SymfonyTextType::class, array(
                    'required' => false,
                ))
                ->add('link_label', SymfonyTextType::class, array(
                    'required' => false,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = array(
            'class' => 'synapse-text-component',
        );
    }
}
