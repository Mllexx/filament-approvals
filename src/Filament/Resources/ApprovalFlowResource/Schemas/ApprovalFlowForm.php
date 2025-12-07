<?php

namespace EightyNine\Approvals\Filament\Resources\ApprovalFlowResource\Schemas;

use App\Filament\Resources\BillableItems\BillableItemsResource;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use EightyNine\Approvals\Services\ModelScannerService;
use Illuminate\Support\HtmlString;

class ApprovalFlowForm
{
    public static function configure(Schema $schema): Schema
    {
        // Scan App\Models for models that extend ApprovableModel
        $modelScanner = new \EightyNine\Approvals\Services\ModelScannerService();
        $models = $modelScanner->getApprovableModels();
        return $schema
            ->components([
                Section::make('')
                        ->columnSpan('full') 
                        ->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                            
                        ])
                        ->schema([
                            Components\TextInput::make("name")
                                            ->columnSpan(fn($context) => $context === 'create' ? 12 : 6)
                                            ->required(),
                            Components\Select::make('approvable_type')
                                        ->columnSpan(fn($context) => $context === 'create' ? 12 : 6)
                                        ->options(function() use ($models) {
                                            // remove 'App\Models\' from the value of models
                                            $models = array_map(function($model) {
                                                return str_replace('App\Models\\', '', $model);
                                            }, $models);
                                            return $models;
                                        })
                                        ->required(),
                            Components\Placeholder::make('warning')
                                        ->visible(fn() => empty($models))
                                        ->columnSpanFull()
                                        ->content(new HtmlString('No models in <b>App\Models</b> extend the <b>ApprovableModel</b>. Please see our documentation.')),
                        ]),
            ]);
    }
}
