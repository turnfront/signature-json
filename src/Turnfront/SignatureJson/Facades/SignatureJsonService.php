<?php
/**
 * @file 
 */

namespace Turnfront\SignatureJson\Facades;

use Illuminate\Support\Facades\Facade as Facade;

class SignatureJsonService extends Facade {

  protected static function getFacadeAccessor(){
    return "Turnfront\\SignatureJson\\Contracts\\SignatureJsonServiceInterface";
  }

}