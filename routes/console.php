<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
Artisan::command('inspire', function () { $this->comment(Inspiring::quote()); })->purpose('Display an inspiring quote');
Artisan::command('sst:alertas', function () {
    $vencidos = \App\Models\ASO::where('data_vencimento','<',now())->count();
    $this->info("ASOs vencidos: {$vencidos}");
})->purpose('Verificar alertas SST')->daily();
