<?php

namespace AcMarche\MeriteSportif\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VoteValidator extends ConstraintValidator
{
    public function validate($candidatures, Constraint $constraint): void
    {
        /* @var $constraint VoteValidator */

        if (null === $candidatures || !is_array($candidatures)) {
            return;
        }

        $totalPoints = 0;

        foreach ($candidatures as $candidature) {
            $point = $candidature['point'];
            $totalPoints += $point;
            if ($point > 2 || $point < 0) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', 'Attribuez une valeur entre 0 et 2')
                    ->addViolation();
            }
        }

        if ($totalPoints !== 3) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', 'Veuillez attribuer maximum 3 points')
                ->addViolation();
        }
    }
}
