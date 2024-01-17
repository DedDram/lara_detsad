<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class CustomValidationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('latin_characters', function ($attribute, $value, $parameters, $validator) {
            $latinCount = preg_match_all('/[a-z]/i', $value, $latin);
            $allCount = strlen($value);

            return ($latinCount / $allCount * 100) <= 30;
        });

        Validator::extend('no_spam_links', function ($attribute, $value, $parameters, $validator) {
            return !preg_match('/\[url|<a/m', $value);
        });
    }
}
