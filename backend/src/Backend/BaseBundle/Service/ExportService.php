<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/11/7
 * Time: 下午12:00
 */

namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Liuggio\ExcelBundle\Factory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @DI\Service("export_service")
 */
class ExportService
{
    public function exportExcelAction($data = array(), $filename = 'tmp.xlsx')
    {
        return $this->export($data, $filename);
    }

    protected function export($data = array(), $filename, $type = 'Excel2007', $contentType = 'text/vnd.ms-excel')
    {
        $phpexcel = new Factory();
        $phpExcelObject = $phpexcel->createPHPExcelObject();

        $this->writeSheet($phpExcelObject->setActiveSheetIndex(0), $data);

        $writer = $phpexcel->createWriter($phpExcelObject, $type);
        /** @var StreamedResponse $response */
        $writer->save("/tmp/{$filename}");
        $response = $phpexcel->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "$filename"
        );

        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

    protected function writeSheet(\PHPExcel_Worksheet $sheet, $array)
    {
        //write head
        foreach($array['keys'] as $index => $column){
            $sheet->setCellValueByColumnAndRow($index, 1, $column);
        }

        //write value
        foreach($array['values'] as $rowIndex => $row){
            foreach($row as $columnIndex => $column) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex + 2, $column);
            }
        }
    }
}