<?php
/**
 * @file 
 */

namespace Turnfront\SignatureJson;

use Illuminate\Support\ServiceProvider;

class SignatureJsonServiceProvider extends ServiceProvider{

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    \App::bind("Turnfront\\SignatureJson\\Contracts\\SignatureJsonServiceInterface", function ($app){
      return new Services\SignatureJsonService();
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array();
  }

} 