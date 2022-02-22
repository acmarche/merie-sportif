<?php


namespace AcMarche\MeriteSportif\Service;

use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;
use Knp\Snappy\Pdf;

class PdfFactory
{
    public function __construct(private CandidatRepository $candidatRepository, private Pdf $pdf, private SluggerInterface $slugger, private Environment $environment)
    {
    }

    public function create(Club $club): string
    {
        $html = $this->environment->render(
            'pdf/proposition_finish.html.twig',
            [
                'club' => $club,
                'candidats' => $this->candidatRepository->getByClub($club)
            ]
        );

        return $this->pdf->getOutputFromHtml($html);
    }
}