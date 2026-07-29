<?php

namespace App\Filament\Employee\Resources\VehicleRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VehicleRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('employee_name')
                    ->searchable(),
                TextColumn::make('department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destination')
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Travel Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('time')
                    ->label('Travel Time')
                    ->time('h:i A')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('return_time')
                    ->label('Return Time')
                    ->time('h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'on_trip' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'on_trip' => 'On Trip',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending (New)',
                        'approved' => 'Approved',
                        'on_trip' => 'On Trip',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending' && !$record->document),
                Action::make('print')
                    ->label('Print Form')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record) => route('vehicle-requests.print', $record->id))
                    ->openUrlInNewTab(),
                Action::make('upload_document')
                    ->label('Upload Document')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('success')
                    ->visible(fn ($record) => ($record->status === 'pending' || $record->status === 'approved') && !$record->document)
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('document')
                            ->label('Upload CEO Signed Document')
                            ->disk('public')
                            ->directory('request-documents')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'document' => $data['document'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Document Uploaded')
                            ->body('CEO Signed Document uploaded successfully! Trip ticket is now active!')
                            ->success()
                            ->send();
                    }),
                Action::make('view_document')
                    ->label('View CEO Form')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->visible(fn ($record) => $record->document !== null)
                    ->url(fn ($record) => asset('storage/' . $record->document))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
