<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->label('Import Users')
                ->color("primary")
                ->sampleExcel(
                    sampleData: [
                        ['emp_id', 'name', 'email', 'password', 'first_name', 'last_name'],
                    ],
                    fileName: 'users.xlsx',
                    sampleButtonLabel: 'Download Sample',
                    customiseActionUsing: fn(Action $action) => $action->color('primary')
                        ->icon('heroicon-m-clipboard')
                )
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    $createdCount = 0; // นับจำนวน record ที่เพิ่มใหม่
                    $collection->each(function ($row) use (&$createdCount) {
                        // ตรวจสอบและกรองเฉพาะฟิลด์ที่ต้องการ
                        $userData = $row->only([
                            'emp_id',
                            'name',
                            'email',
                            'password',
                            'first_name',
                            'last_name',
                        ])->toArray(); // แปลงเป็น array
        
                        // เข้ารหัสรหัสผ่าน
                        $userData['password'] = Hash::make($userData['password']);
                        $userData['corp_id'] = 1;

                        // สร้างหรืออัปเดตข้อมูลในฐานข้อมูล
                        $user = User::updateOrCreate(
                            ['email' => $userData['email']], // ใช้ email เป็นคีย์สำหรับอัปเดต
                            $userData,
                        );

                        // ถ้าเป็นการสร้างใหม่ให้นับ
                        if ($user->wasRecentlyCreated) {
                            $createdCount++;
                        }

                        // กำหนดค่า role เริ่มต้น
                        $defaultRole = 'user'; // เปลี่ยนเป็น role ที่ต้องการ
                        if (!$user->hasRole($defaultRole)) {
                            $user->assignRole($defaultRole);
                        }
                    });
                    // ส่ง Notification เมื่อเสร็จ
                    Notification::make()
                        ->title('Import Successful')
                        ->body("Successfully imported {$createdCount} new users.")
                        ->success()
                        ->send();

                    return $collection;
                }),
        ];
    }
}
