<?php

namespace App\Controller\Admin;

use App\Entity\Pokemon;
use App\Workflow\IPokemonWorkflow;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class PokemonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pokemon::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AvatarField::new('avatarUrl')
            ->setHeight($pageName === Crud::PAGE_DETAIL ? 96 : 48);


        /** @var Field $field */
        foreach (parent::configureFields($pageName) as $field) {
            if ($field->getAsDto()->getPropertyNameWithSuffix() === 'marking') {
                $choices = [IPokemonWorkflow::PLACE_NEW, IPokemonWorkflow::PLACE_FETCHED];
//                dd($choices);
                yield ChoiceField::new('marking')->setChoices(
                    array_combine($choices, $choices)
                );

            } else {
                yield $field;
            }
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('marking')
            ->add('name')
            ->add(BooleanFilter::new('owned'))
            ;
    }
}
