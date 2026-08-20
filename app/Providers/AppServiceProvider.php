<?php

namespace App\Providers;

use App\Models\Tmcontr;
use App\Models\Trchartacct;
use App\Models\Trorg;
use App\Models\Vororg;
use App\Models\Vpon;
use App\Repositories\TmcontrRepository;
use App\Repositories\TrchartacctRepository;
use App\Repositories\TrorgRepository;
use App\Repositories\VororgRepository;
use App\Repositories\VponRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ponytail: invalidasi cache dropdown saat master berubah. 4 model, 1 file.
        $flushEvents = ['saved', 'deleted', 'restored'];

        foreach ($flushEvents as $event) {
            Tmcontr::$event(fn (Tmcontr $m) => TmcontrRepository::flushCache($m->c_org_contr));
            Vororg::$event(fn () => VororgRepository::flushCache());
            Trchartacct::$event(fn () => TrchartacctRepository::flushCache());
            Vpon::$event(fn () => VponRepository::flushCache());
        }

        // Trorg tanpa SoftDeletes — tidak punya event restored().
        foreach (['saved', 'deleted'] as $event) {
            Trorg::$event(fn () => TrorgRepository::flushCache());
        }
    }
}
