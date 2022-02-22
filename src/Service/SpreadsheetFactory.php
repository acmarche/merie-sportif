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
        $sheet = $spreadsheet->getActiveSheet();

        $c = 1;
        $sheet
            ->setCellValue('A'.$c, 'Candidat')
            ->setCellValue('B'.$c, 'Club')
            ->setCellValue('C'.$c, 'Catégorie')
            ->setCellValue('D'.$c, 'Points')
            ->setCellValue('E'.$c, 'Date');

        $ligne = 2;

        foreach ($votes as $vote) {
            $colonne = 'A';
            $sheet->setCellValue($colonne.$ligne, $vote->getCandidat());
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $vote->getClub());
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $vote->getCategorie());
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $vote->getPoint());
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $vote->getCreatedAt()->format('d-m-Y H:i'));
            ++$ligne;
        }

        return $spreadsheet;
    }

    public function downloadXls(Spreadsheet $phpExcelObject, string $fileName): BinaryFileResponse
    {
        $writer = new Xlsx($phpExcelObject);
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        try {
            $writer->save($temp_file);
        } catch (Exception) {
        }

        $response = new BinaryFileResponse($temp_file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $fileName ?? $response->getFile()->getFilename()
        );

        return $response;
    }
}
