<?php
/**
 * @file
 */
namespace Turnfront\SignatureJson\Contracts;

interface SignatureJsonServiceInterface {
  /**
   *  Accepts a signature created by signature pad in Json format
   *  Converts it to an image resource
   *  The image resource can then be changed into png, jpg whatever PHP GD supports
   *
   *  To create a nicely anti-aliased graphic the signature is drawn 12 times it's original size then shrunken
   *
   * @param string|array $json
   *
   * @return object
   */
  public function signatureJsonToImage($json);
}