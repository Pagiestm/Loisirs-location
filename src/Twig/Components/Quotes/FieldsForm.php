<?php

namespace App\Twig\Components\Quotes;

use App\Entity\Quote\Quote;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/quotes/fields_form.html.twig')]
final class FieldsForm
{
    use DefaultActionTrait;

    #[LiveProp]
    public Quote $quote;
}
