<?php


namespace AcMarche\MeriteSportif\Service;

use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Twig\Environment;
use Knp\Snappy\Pdf;

class PdfFactory
{
    public function __construct(
        private readonly CandidatRepository $candidatRepository,
        private readonly Pdf $pdf,
        private readonly Environment $twigEnvironment
    ) {
    }

    public function createForProposition(Club $club): string
    {
        $html = $this->twigEnvironment->render(
            '@AcMarcheMeriteSportif/pdf/proposition_finish.html.twig',
            [
                'club' => $club,
                'candidats' => $this->candidatRepository->getByClub($club),
            ]
        );

        return $this->pdf->getOutputFromHtml($html);
    }
}