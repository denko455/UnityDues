<?php

namespace App\Filament\Resources\MembersResource\Pages;

use App\Filament\Resources\MembersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use App\Models\Residences;

use Filament\Tables\Actions\Action;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

class ListMembers extends ListRecords
{
    protected static string $resource = MembersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('Članovi');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Ime i Prezime')
                    ->toggleable(),
                TextColumn::make('id_number')
                    ->label('Lični broj')
                    ->toggleable()
                    ->default("-"),                
                TextColumn::make('residence.name')
                    ->label('Prebivalište')
                    ->toggleable()
                    ->default("-"),
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable()
                    ->default("-"),
                TextColumn::make('tel')
                    ->label("tel.")
                    ->toggleable()
                    ->default("-"),
            ])
            ->filters([
                SelectFilter::make('residence_id')
                ->label('Prebivalište')
                ->multiple()
                ->options(Residences::pluck("name", "id")->all()),
            ], FiltersLayout::Modal)
            ->deferFilters()
            ->headerActions([
                Action::make("dom_pdf_export")
                    ->hiddenLabel()
                    ->color("danger")
                    ->icon("heroicon-o-document-chart-bar")
                    ->url(function () {
                        $filter = base64_encode( json_encode([ "rows" =>$this->tableFilters, "columns"=> $this->toggledTableColumns]));
                        return route('pdfMembers', ["filter" => $filter]);
                    })
                    ->openUrlInNewTab()
            ]);
    }
    
}
