<?php


namespace AcMarche\MeriteSportif\Service;

use AcMarche\MeriteSportif\Entity\Vote;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SpreadsheetFactory
{
    /**
     * @param Vote[] $votes
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function createXSL(array $votes): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $c = 1;
        $worksheet
            ->setCellValue('A'.$c, 'Candidat')
            ->setCellValue('B'.$c, 'Club')
            ->setCellValue('C'.$c, 'Catégorie')
            ->setCellValue('D'.$c, 'Points')
            ->setCellValue('E'.$c, 'Date');

        $ligne = 2;

        foreach ($votes as $vote) {
            $colonne = 'A';
            $worksheet->setCellValue($colonne.$ligne, $vote->getCandidat());
            ++$colonne;
            $worksheet->setCellValue($colonne.$ligne, $vote->getClub());
            ++$colonne;
            $worksheet->setCellValue($colonne.$ligne, $vote->getCategorie());
            ++$colonne;
            $worksheet->setCellValue($colonne.$ligne, $vote->getPoint());
            ++$colonne;
            $date = $vote->getCreatedAt();
            if ($date) {
                $date = $date->format('d-m-Y H:i');
            }

            $worksheet->setCellValue($colonne.$ligne, $date);
            ++$ligne;
        }

        return $spreadsheet;
    }

    public function downloadXls(Spreadsheet $spreadsheet, string $fileName): BinaryFileResponse
    {
        $xlsx = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        try {
            $xlsx->save($temp_file);
        } catch (Exception) {
        }

        $binaryFileResponse = new BinaryFileResponse($temp_file);
        $binaryFileResponse->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $fileName ?? $binaryFileResponse->getFile()->getFilename()
        );

        return $binaryFileResponse;
    }
}
