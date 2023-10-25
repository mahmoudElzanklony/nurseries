<?php


namespace App\Providers;


use App\Http\traits\messages;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MacroServiceProvider extends ServiceProvider
{
    public function boot() : void{
        \Illuminate\Database\Eloquent\Builder::macro('findOrFailWithCustomError', function ($id, $error_message = '') {

            try {
                return $this->findOrFail($id);
            } catch (\Throwable $exception) {
                return messages::error_output($error_message != '' ? $error_message : trans('errors.no_data'));
            }
        });
    }
}
