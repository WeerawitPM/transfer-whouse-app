<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasUser;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Auth;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;
    use HasUser;

    public ?array $data = [];
    public $userClass;
    protected static int $sort = 0;

    public function mount(): void
    {
        $this->user = $this->getUser();

        $this->userClass = get_class($this->user);

        $this->form->fill($this->user->only('avatar_url', 'name', 'email', 'first_name', 'last_name'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament-edit-profile::default.profile_information'))
                    ->description(__('filament-edit-profile::default.profile_information_description'))
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->alignCenter()
                            ->label(__('filament-edit-profile::default.avatar'))
                            ->avatar()
                            ->imageEditor()
                            ->disk(config('filament-edit-profile.disk', 'public'))
                            ->visibility(config('filament-edit-profile.visibility', 'public'))
                            ->directory(filament('filament-edit-profile')->getAvatarDirectory())
                            ->rules(filament('filament-edit-profile')->getAvatarRules())
                            ->hidden(!filament('filament-edit-profile')->getShouldShowAvatarForm()),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament-edit-profile::default.name'))
                                    ->required()
                                    ->readOnly(!Auth::user()->hasRole('super_admin')),
                                TextInput::make('email')
                                    ->label(__('filament-edit-profile::default.email'))
                                    ->email()
                                    ->required()
                                    ->unique($this->userClass, ignorable: $this->user)
                                    ->readOnly(!Auth::user()->hasRole('super_admin')),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->readOnly(!Auth::user()->hasRole('super_admin')),
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->readOnly(!Auth::user()->hasRole('super_admin')),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }


    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->user->update($data);
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-edit-profile::default.saved_successfully'))
            ->send();
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
