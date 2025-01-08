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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    public function __construct(
        #[Autowire(env: 'MERITE_EMAIL')]
        private readonly string $email,
        private readonly MailerInterface $mailer,
        private readonly ClubRepository $clubRepository,
        private readonly CandidatRepository $candidatRepository,
        private readonly PdfFactory $pdfFactory,
        private readonly VoteService $voteService,
        private readonly RequestStack $requestStack
    ) {

    }

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
            $this->send($message);
        }
    }

    protected function send(Email $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $transportException) {
            $flashBag = $this->requestStack->getSession()->getFlashBag();
            $flashBag->add('danger', $transportException->getMessage());
        }
    }

    protected function createMessage(array $data, Club $club, string $value): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($data['from'])
            ->to($club->getEmail())
            ->bcc($this->email)
            //  ->bcc('jf@marche.be')
            ->subject($data['sujet'])
            ->text($data['texte'])
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_content.html.twig')
            ->context(
                [
                    'club' => $club,
                    'texte' => $data['texte'],
                    'value' => $value,
                ]
            );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function newPropositionMessage(Candidat $candidat, Club $club): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from($club->getEmail())
            //->to($club->getEmail())
            ->addTo($this->email)
            ->addTo('jd@marche.be')
            ->subject('Une nouvelle proposition pour le mérite')
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_proposition.html.twig')
            ->context(
                [
                    'club' => $club,
                    'candidat' => $candidat,
                ]
            );

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function propositionFinish(Club $club): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from($this->email)
            ->to($club->getEmail())
            //->addTo('jf@marche.be')
            ->bcc($this->email)
            ->subject('Vos propositions pour le Challenge & Mérites Sportifs')
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_proposition_finish.html.twig')
            ->context(
                [
                    'club' => $club,
                    'candidats' => $this->candidatRepository->getByClub($club),
                ]
            );

        $pdf = $this->pdfFactory->createForProposition($club);

        if ($pdf !== '' && $pdf !== '0') {
            $templatedEmail->attach(
                $pdf,
                'propositions.pdf'
            );
        }

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function votesFinish(Club $club): void
    {
        $votes = $this->voteService->getVotesByClub($club);
        $templatedEmail = (new TemplatedEmail())
            ->from($this->email)
            ->to($club->getEmail())
            //->addTo('jf@marche.be')
            ->bcc($this->email)
            ->subject('Vos votes pour le Challenge & Mérites Sportifs')
            ->htmlTemplate('@AcMarcheMeriteSportif/message/_vote_finish.html.twig')
            ->context(
                [
                    'club' => $club,
                    'votes' => $votes,
                    'candidats' => $this->candidatRepository->getByClub($club),
                ]
            );

        $this->mailer->send($templatedEmail);
    }
}
