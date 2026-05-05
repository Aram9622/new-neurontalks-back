<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ArtisanConsole extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static string $view = 'filament.pages.artisan-console';

    protected static ?string $navigationLabel = 'Artisan Console';

    protected ?string $heading = 'Artisan Console';

    public ?string $command = '';
    public ?string $output = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('command')
                    ->label('Command')
                    ->placeholder('e.g. migrate --force')
                    ->required(),
                Textarea::make('output')
                    ->label('Output')
                    ->readonly()
                    ->rows(15)
                    ->extraAttributes(['class' => 'font-mono bg-gray-900 text-green-400']),
            ]);
    }

    public function runCommand(): void
    {
        $this->validate();

        $outputBuffer = new BufferedOutput();

        try {
            Artisan::call($this->command, [], $outputBuffer);
            $this->output = $outputBuffer->fetch();
        } catch (\Exception $e) {
            $this->output = 'Error: ' . $e->getMessage();
        }
    }
}
