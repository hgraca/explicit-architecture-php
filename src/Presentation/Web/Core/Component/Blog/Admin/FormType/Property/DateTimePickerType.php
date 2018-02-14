<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Presentation\Web\Core\Component\Blog\Admin\FormType\Property;

use Acme\PhpExtension\DateTime\MomentFormatConverter;
use Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the custom form field type used to manipulate datetime values across
 * Bootstrap Date\Time Picker javascript plugin.
 *
 * See https://symfony.com/doc/current/cookbook/form/create_custom_field_type.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class DateTimePickerType extends AbstractType
{
    /**
     * @var MomentFormatConverter
     */
    private $formatConverter;

    public function __construct(MomentFormatConverter $converter)
    {
        $this->formatConverter = $converter;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-date-format'] = $this->formatConverter->convert($options['format']);
        $view->vars['attr']['data-date-locale'] = mb_strtolower(str_replace('_', '-', Locale::getDefault()));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
        ]);
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }
}
