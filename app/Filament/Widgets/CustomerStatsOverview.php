<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use App\Models\IpLog;
use App\Models\NewsletterSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Subscribers', NewsletterSubscription::query()->count())
                ->description('Newsletter subscribers')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
            Stat::make('Audit requests', Audit::query()->count())
                ->description('Submitted audit requests')
                ->descriptionIcon('heroicon-m-document-magnifying-glass')
                ->color('warning'),
            Stat::make('Visits', IpLog::query()->count())
                ->description('Logged API requests')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),
        ];
    }
}
