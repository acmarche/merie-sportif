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
use AcMarche\MeriteSportif\Repository\ClubRepository;
use AcMarche\MeriteSportif\Setting\SettingService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class Mailer
{
    public function __construct(
        private MailerInterface $mailer,
        private ClubRepository $clubRepository,
        private CandidatRepository $candidatRepository,
        private PdfFactory $pdfFactory,
        private VoteService $voteService,
        private SettingService $settingService,
        private RequestStack $requestStack,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function handle(array $data): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();

        foreach ($this->clubRepository->findAll() as $club) {
            $user = $club->getUser();
            if ($user === null) {
                $flashBag->add('error', $club->getNom().' a pas de compte user');
                continue;
            }

            $token = $user->getToken();
            if ($token === null) {
                $flashBag->add('error', $club->getNom().' a pas de token');
                continue;
            }

            $value = $token->getValue();

            $message = $this->createMessage($data, $club, $value);
            $this->mailer->send($message);
        }
    }

    protected function createMessage(array $data, Club $club, string $value): TemplatedEmail
    {
        $emails = $this->settingService->emails();
        $email = $this->settingService->emailFrom();

        return (new TemplatedEmail())
            ->from(new Address($email, $club->getEmail()))
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
        $email = $this->settingService->emailFrom();
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($email, $club->getEmail()))
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
        $email = $this->settingService->emailFrom();
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($email))
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
        $email = $this->settingService->emailFrom();
        $votes = $this->voteService->getVotesByClub($club);
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($email))
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