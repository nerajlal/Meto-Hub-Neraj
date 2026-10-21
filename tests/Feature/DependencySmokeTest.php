<?php

namespace Tests\Feature;

use Barryvdh\DomPDF\Facade\Pdf;
use Minishlink\WebPush\VAPID;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class DependencySmokeTest extends TestCase
{
    public function test_pdf_renderer_still_generates_a_document(): void
    {
        $pdf = Pdf::loadHTML('<h1>Test invoice</h1><table><tr><td>Total</td><td>100.00 INR</td></tr></table>')->output();
        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertGreaterThan(500, strlen($pdf));
    }

    public function test_spreadsheet_export_and_import_round_trip(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'metohub-sheet-');
        try {
            $sheet = new Spreadsheet;
            $sheet->getActiveSheet()->setCellValue('A1', 'Product')->setCellValue('B1', 123.45);
            IOFactory::createWriter($sheet, 'Xlsx')->save($path);
            $loaded = IOFactory::load($path);
            $this->assertSame('Product', $loaded->getActiveSheet()->getCell('A1')->getValue());
            $this->assertEquals(123.45, $loaded->getActiveSheet()->getCell('B1')->getValue());
            $loaded->disconnectWorksheets();
            $sheet->disconnectWorksheets();
        } finally {
            if (is_file($path)) unlink($path);
        }
    }

    public function test_push_keys_and_signed_headers_generate_without_network(): void
    {
        $vapid = VAPID::validate(VAPID::createVapidKeys() + ['subject' => 'mailto:test@example.test']);
        $headers = VAPID::getVapidHeaders('https://fcm.googleapis.com', $vapid['subject'], $vapid['publicKey'], $vapid['privateKey'], 'aes128gcm');
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertStringStartsWith('vapid ', $headers['Authorization']);
    }
}
