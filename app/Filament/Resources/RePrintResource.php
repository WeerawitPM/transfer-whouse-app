<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RePrintResource\Pages;
use App\Filament\Resources\TransferBookMenuResource\Functions\printDocument;
use App\Filament\Resources\TransferBookMenuResource\Functions\printTag;
use App\Models\JobHead;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;
use Auth;

class RePrintResource extends Resource
{
    protected static ?string $model = JobHead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Re-Print';
    protected static ?string $breadcrumb = 'Re-Print';
    protected static ?string $label = 'Re-Print';
    protected static ?string $pluralLabel = 'Re-Print';
    protected static ?string $navigationGroup = 'Print';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Job Details')
                    ->tabs([
                        Tab::make('Job Head')
                            ->schema([
                                Grid::make(2) // Two-column grid for JobHead fields
                                    ->schema([
                                        TextInput::make('job_no')
                                            ->label('Job No')
                                            ->maxLength(255),
                                        TextInput::make('department')
                                            ->label('Department')
                                            ->maxLength(255),
                                        TextInput::make('from_whs')
                                            ->label('From Warehouse')
                                            ->maxLength(255),
                                        TextInput::make('to_whs')
                                            ->label('To Warehouse')
                                            ->maxLength(255),
                                        DatePicker::make('created_date')
                                            ->label('Created Date'),
                                        Select::make('user_id')
                                            ->label('User')
                                            ->relationship('user', 'name'),
                                    ]),
                            ]),
                        Tab::make('Job Details')
                            ->schema([
                                Repeater::make('jobDetails')
                                    ->relationship('jobDetails')
                                    ->schema([
                                        TextInput::make('kanban')
                                            ->label('Kanban')
                                            ->disabled(),
                                        TextInput::make('part_no')
                                            ->label('Part No')
                                            ->disabled(),
                                        TextInput::make('part_name')
                                            ->label('Part Name')
                                            ->disabled(),
                                        TextInput::make('model')
                                            ->label('Model')
                                            ->disabled(),
                                        TextInput::make('qty')
                                            ->label('Quantity')
                                            ->disabled(),
                                    ])
                                    ->disableItemMovement()
                                    ->disabled()
                            ]),
                        Tab::make('Job To Tags')
                            ->schema([
                                Repeater::make('jobToTags')
                                    ->relationship('jobToTags')
                                    ->schema([
                                        TextInput::make('kanban')
                                            ->label('Kanban')
                                            ->disabled(),
                                        TextInput::make('part_no')
                                            ->label('Part No')
                                            ->disabled(),
                                        TextInput::make('part_name')
                                            ->label('Part Name')
                                            ->disabled(),
                                        TextInput::make('model')
                                            ->label('Model')
                                            ->disabled(),
                                        TextInput::make('qty')
                                            ->label('Quantity')
                                            ->disabled(),
                                    ])
                                    ->disableItemMovement()
                                    ->disabled()
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('job_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_whs')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_whs')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->button()
                    ->color('primary'),
                Action::make("print_document")
                    ->button()
                    ->icon('heroicon-o-printer')
                    ->label('Document')
                    ->action(function ($record) {
                        // dd($record->job_no);
                        return printDocument::print_document_one($record->job_no);
                    }),
                Action::make("print_tag")
                    ->button()
                    ->icon('heroicon-o-printer')
                    ->label('Tags')
                    ->action(function ($record) {
                        // dd($record->job_no);
                        return printTag::print_tags_one($record->job_no);
                    }),
            ])
            ->bulkActions([
                BulkAction::make('print')
                    // ->requiresConfirmation()
                    ->action(fn(Collection $records) => self::print ($records))
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRePrints::route('/'),
        ];
    }

    public static function print($records)
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/public/documents.zip');
        $pdfFiles = [];  // เก็บ paths ของไฟล์ PDF ที่สร้างขึ้น

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($records as $record) {
                // รับ path ของไฟล์ PDF ที่สร้างขึ้นจาก printDocument
                $documentPath = printDocument::print_document($record->job_no);
                if ($documentPath) {
                    $zip->addFile($documentPath, basename($documentPath));
                    $pdfFiles[] = $documentPath; // เก็บ path ของไฟล์ PDF ไว้สำหรับลบภายหลัง
                }

                // รับ path ของไฟล์ PDF ที่สร้างขึ้นจาก printTag
                $tagPath = printTag::print_tags($record->job_no);
                if ($tagPath) {
                    $zip->addFile($tagPath, basename($tagPath));
                    $pdfFiles[] = $tagPath; // เก็บ path ของไฟล์ PDF ไว้สำหรับลบภายหลัง
                }
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }

        // ลบไฟล์ PDF ที่เก็บไว้หลังจาก ZIP เสร็จสิ้น
        foreach ($pdfFiles as $pdfFile) {
            if (file_exists($pdfFile)) {
                unlink($pdfFile);
            }
        }

        // ดาวน์โหลด ZIP ไฟล์
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }
}
