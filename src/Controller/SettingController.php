<?php

namespace AcMarche\MeriteSportif\Controller;

use AcMarche\MeriteSportif\Entity\Setting;
use AcMarche\MeriteSportif\Form\SettingType;
use AcMarche\MeriteSportif\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/setting')]
#[IsGranted('ROLE_MERITE_ADMIN')]
class SettingController extends AbstractController
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
    ) {}

    #[Route(path: '/', name: 'setting_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMeriteSportif/setting/index.html.twig',
            [
                'setting' => $this->settingRepository->findOne(),
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'setting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Setting $setting): Response
    {
        if (count($setting->emails) < 2) {
            $setting->emails = [$setting->emails[0], ""];
        }

        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->settingRepository->flush();
            $this->addFlash('success', 'Les paramètres ont bien été modifiés');

            return $this->redirectToRoute('setting_index');
        }

        return $this->render(
            '@AcMarcheMeriteSportif/setting/edit.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

}
