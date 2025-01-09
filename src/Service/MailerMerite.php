<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 8/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Service;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Setting\SettingService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class MailerMerite
{
    public function __construct(
        public MailerInterface $mailer,
        private CandidatRepository $candidatRepository,
        private PdfFactory $pdfFactory,
        private VoteService $voteService,
        private SettingService $settingService,
    ) {}

    public function createMessage(array $data, Club $club, string $value): TemplatedEmail
    {
        $emails = $this->settingService->emails();
        $emailFrom = $this->settingService->emailFrom();

        return (new TemplatedEmail())
            ->from(new Address($emailFrom))
            ->to($club->getEmail())
            ->bcc(...$emails)
            ->subject($data['sujet'])
            ->text($data['texte'])
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_content.html.twig')
            ->context(
                [
                    'club' => $club,
                    'texte' => $data['texte'],
                    'value' => $value,
                ],
            );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function newPropositionMessage(Candidat $candidat, Club $club): void
    {
        $emails = $this->settingService->emails();
        $emailFrom = $this->settingService->emailFrom();
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($emailFrom, $club->getEmail()))
            ->addTo(...$emails)
            ->subject('Une nouvelle proposition pour le mérite')
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_proposition.html.twig')
            ->context(
                [
                    'club' => $club->getNom(),
                    'candidatNom' => $candidat->getNom(),
                    'candidatUuid' => $candidat->getUuid(),
                ],
            );
        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function propositionFinish(Club $club): void
    {
        $emails = $this->settingService->emails();
        $emailFrom = $this->settingService->emailFrom();
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($emailFrom))
            ->to($club->getEmail())
            ->bcc(...$emails)
            ->subject('Vos propositions pour le Challenge & Mérites Sportifs')
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_proposition_finish.html.twig')
            ->context(
                [
                    'club' => $club->getNom(),
                ],
            );

        $pdf = $this->pdfFactory->createForProposition($club);

        if ($pdf !== '' && $pdf !== '0') {
            $templatedEmail->attach(
                $pdf,
                'propositions.pdf',
            );
        }

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function votesFinish(Club $club): void
    {
        $emails = $this->settingService->emails();
        $emailFrom = $this->settingService->emailFrom();
        $votes = $this->voteService->getVotesByClub($club);
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($emailFrom))
            ->to($club->getEmail())
            ->bcc(...$emails)
            ->subject('Vos votes pour le Challenge & Mérites Sportifs')
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_vote_finish.html.twig')
            ->context(
                [
                    'club' => $club,
                    'votes' => $votes,
                    'candidats' => $this->candidatRepository->getByClub($club),
                ],
            );

        $this->mailer->send($templatedEmail);
    }
}