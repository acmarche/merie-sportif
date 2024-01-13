<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 7/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CandidatToNumberTransformer implements DataTransformerInterface
{
    public function __construct(private CandidatRepository $candidatRepository)
    {
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param Candidat|null $issue
     * @return string
     */
    public function transform($issue): mixed
    {
        if (!$issue instanceof Candidat) {
            return '';
        }

        return $issue->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param string $issueNumber
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($issueNumber) : ?Candidat
    {
        // no issue number? It's optional, so that's ok
        if ($issueNumber === '' || $issueNumber === '0') {
            return null;
        }

        $issue = $this->candidatRepository->find($issueNumber);

        if (!$issue instanceof Candidat) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(
                sprintf(
                    'An issue with number "%s" does not exist!',
                    $issueNumber
                )
            );
        }

        return $issue;
    }
}


