<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 2/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Repository\VoteRepository;
use AcMarche\MeriteSportif\Service\SpreadsheetFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    public function __construct(private VoteRepository $voteRepository, private SpreadsheetFactory $spreadsheetFactory)
    {
    }

    #[Route(path: '/', name: 'merite_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/default/index.html.twig',
            [

            ]
        );
    }

    #[Route(path: '/contact', name: 'merite_contact', methods: ['GET', 'POST'])]
    public function contact(): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/default/contact.html.twig',
            [

            ]
        );
    }

    #[Route(path: '/resultat', name: 'merite_resultat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function resultat(): Response
    {
        $votes = $this->voteRepository->getAll();

        return $this->render(
            '@AcMarcheMeriteSportif/default/resultat.html.twig',
            [
                'votes' => $votes,
            ]
        );
    }

    #[Route(path: '/export', name: 'merite_vote_export', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MERITE_ADMIN')]
    public function export(): Response
    {
        $votes = $this->voteRepository->getAll();
        $xls = $this->spreadsheetFactory->createXSL($votes);

        return $this->spreadsheetFactory->downloadXls($xls, 'votes.xlsx');
    }

}
