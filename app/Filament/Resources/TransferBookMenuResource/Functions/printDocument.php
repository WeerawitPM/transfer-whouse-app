<?php

namespace App\Filament\Resources\TransferBookMenuResource\Functions;

use App\Models\JobHead;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Auth;

class printDocument
{
    public static function print_document($job_no)
    {
        $job = JobHead::where('job_no', $job_no)->first();
        $change_job_name = str_replace('/', '-', $job_no);
        // dd($change_job_name);
        $jobId = $job->id;
        $file_name = "Document_$change_job_name.pdf";
        // dd($file_name);

        // ตั้งค่า Jasper Server
        $jasperServer = env('JASPER_SERVER', 'http://localhost:8080');
        $jasperUser = env('JASPER_USER', 'jasperadmin');
        $jasperPassword = env('JASPER_PASSWORD', 'jasperadmin');

        // URL สำหรับการเข้าถึงรายงาน
        $urlReport = "{$jasperServer}/jasperserver/rest_v2/reports/vcst_report/document_vcst.pdf?ParmID={$jobId}";

        try {
            $response = Http::withBasicAuth($jasperUser, $jasperPassword)
                ->withHeaders(['Accept' => 'application/pdf'])
                ->get($urlReport);

            if ($response->successful()) {
                $recipient = Auth::user();
                // dd($recipient);
                Notification::make()
                    ->title('ปริ้น Document สำเร็จ')
                    ->success()
                    ->body("You can download your document [here]($urlReport).")
                    ->color('success')
                    ->sendToDatabase($recipient);
                return response()->streamDownload(function () use ($response) {
                    echo $response->body();
                }, $file_name, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename=' . $file_name,
                ]);
            } else {
                Notification::make()
                    ->title('เกิดข้อผิดพลาดในการสร้างรายงาน')
                    ->danger()
                    ->send();
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('เกิดข้อผิดพลาดในการเชื่อมต่อกับ Jasper Server')
                ->danger()
                ->send();
            return redirect()->back();
        }
    }
}