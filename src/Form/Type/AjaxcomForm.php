<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AjaxcomForm.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class AjaxcomForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'attr' => [
                        'novalidate' => 'novalidate',
                        'data-ajaxcom' => '',
                    ],
            ]
        );
    }
}
