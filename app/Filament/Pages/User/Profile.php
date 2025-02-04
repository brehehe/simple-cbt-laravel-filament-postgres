<?php

namespace App\Filament\Pages\User;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $view = 'filament.pages.user.profile';

    protected static ?string $navigationLabel = 'Data Profil';

    protected static ?string $modelLabel = 'Data Profil';

    protected static ?string $slug = 'master/profil';

    protected static ?int $navigationSort = 35;

    protected static ?string $title = 'Data Profil';

    protected static ?string $navigationGroup = 'Master';

    public ?array $data = [];

    public function mount() {
        $user = Auth::user();

        $this->data = [
            'id' => $user->id,
            'name'=>$user->name,
            'username'=>$user->username,
            'email'=>$user->email,
            'phone'=>$user->phone,
            'photo'=>$user->photo,
            'password'=>null,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                //
                Section::make('')
                ->columns(12)
                ->columnSpan(12)
                ->schema([
                    FileUpload::make('photo')
                        ->label('Foto')
                        ->openable()
                        ->downloadable()
                        ->image()
                        ->avatar()
                        ->directory('uploads/user')
                        ->optimize('webp')
                        ->columnSpan(2)
                        ->resize(50), // If you want to make it optional

                    TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->columnSpan(10)
                    ->placeholder('Masukkan Nama'),

                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->columnSpan(12)
                        ->placeholder('Masukkan Username')
                        ->rules(['regex:/^\S*$/']) // Validasi untuk tidak mengandung spasi
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('username', preg_replace('/\s+/', '', $state)); // Hapus spasi otomatis
                        }),

                    TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->nullable()
                        ->numeric()
                        ->columnSpan(12)
                        ->placeholder('Masukkan Nomor Telepon'),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->columnSpan(12)
                        ->placeholder('Masukkan Email'),

                    TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->columnSpan(12)
                    ->placeholder('Masukkan Password'),

                ])
            ])
            ->statePath('data');
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('update')
                ->label('Rubah Profil')
                ->icon('fas-user')
                ->action(function () {
                    $this->editUser();
                })
                // ->url(route('your.route.name'))
                ->color('primary'),
        ];
    }

    public function editUser()
    {
        // Validasi data yang diterima dari form
        $this->validate([
            'data.name' => 'required|string|max:255',
            'data.username' => 'required|string|unique:users,username,' . $this->data['id'] . '|regex:/^\S*$/',
            'data.email' => 'required|email|unique:users,email,' . $this->data['id'],
            'data.phone' => 'nullable|numeric',
            'data.password' => 'nullable|min:8',
            // 'data.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB Max
        ]);

        $data = $this->form->getState();


        // Update data user
        $user = Auth::user();

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->password = $data['password'] ? Hash::make($data['password']) : $user->password;
        $user->photo = $data['photo'] ?? null;

        // Simpan perubahan
        $user->save();

        Notification::make()
        ->title('Berhasil!')
        ->body('User Berhasil Dirubah!')
        ->success()
        ->send();
    }
}
