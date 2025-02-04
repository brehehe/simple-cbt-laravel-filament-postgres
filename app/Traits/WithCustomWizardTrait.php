<?php

namespace App\Traits;

use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;

trait WithCustomWizardTrait
{
    public $currentStep = 1;
    public $steps = [];

    public function nextStep()
    {
        if ($this->currentStep < count($this->steps)) {
            if ($this->_nextStepValidation()) {
                $this->currentStep++;
                $this->data['currentStep'] = $this->currentStep;
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->data['currentStep'] = $this->currentStep;
        }
    }

    public function submitStepAction(): Action
    {
        return Action::make('submitstep')
            ->visible($this->currentStep === count($this->steps) ? true : false)
            ->label('Submit')
            ->color('success')
            ->modalHeading('Simpan Data ?')
            ->size(ActionSize::ExtraLarge)
            ->icon('fas-circle-check')
            ->iconSize(IconSize::Small)
            ->iconPosition(IconPosition::After)
            ->requiresConfirmation()
            ->action(fn() => $this->_submitStep());
    }
}