<?php
/**
 *  Signature to Image: A supplemental script for Signature Pad that
 *  generates an image of the signature’s JSON output server-side using PHP.
 *
 *  @project ca.thomasjbradley.applications.signaturetoimage
 *  @author Thomas J Bradley <hey@thomasjbradley.ca>
 *  @link http://thomasjbradley.ca/lab/signature-to-image
 *  @link http://github.com/thomasjbradley/signature-to-image
 *  @copyright Copyright MMXI–, Thomas J Bradley
 *  @license New BSD License
 *  @version 1.1.0
 *
 *  Modified by Andrew Rumble to fit the code style to the rest of the project and to fit with Laravel better.
 */

namespace Turnfront\SignatureJson\Services;


use Turnfront\SignatureJson\Contracts\SignatureJsonServiceInterface;

class SignatureJsonService implements SignatureJsonServiceInterface {

  protected $width = 266;
  protected $height = 103;
  protected $bgColour = array(
    "red" => 0xff,
    "green" => 0xff,
    "blue" => 0xff
  );
  protected $penWidth = 2;
  protected $penColour = array(
    "red" => 0x14,
    "green" => 0x53,
    "blue" => 0x94
  );
  protected $drawMultiplier = 12;

  /**
   * @param array $options OPTIONAL; the options for image creation
   *    imageSize => array(width, height)
   *    bgColour => array(red, green, blue) | transparent
   *    penWidth => int
   *    penColour => array(red, green, blue)
   *    drawMultiplier => int
   */
  public function __construct($options = array()){
    $this->setOptions($options);
  }

  protected function setOptions($options){
    if (!empty($options)){
      foreach ($options as $option => $value){
        $this->$option = $value;
      }
    }
  }

  public function setOption($option, $value){
    $this->$option = $value;
    return $this;
  }

  /**
   * Get the signature image as a string ready to write to a file or the screen.
   *
   * @param $json
   *
   * @return string
   */
  public function getSignature($json){
    $image = $this->signatureJsonToImage($json);
    ob_start();
    imagepng($image);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }

  /**
   *  Accepts a signature created by signature pad in Json format
   *  Converts it to an image resource
   *  The image resource can then be changed into png, jpg whatever PHP GD supports
   *
   *  To create a nicely anti-aliased graphic the signature is drawn 12 times it's original size then shrunken
   *
   *  @param string|array $json
   *
   *
   *  @return object
   */
  public function signatureJsonToImage($json) {
    $img = imagecreatetruecolor($this->width * $this->drawMultiplier, $this->height * $this->drawMultiplier);
    if ($this->bgColour == 'transparent') {
      imagesavealpha($img, true);
      $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
    } else {
      $bg = imagecolorallocate($img, $this->bgColour["red"], $this->bgColour["green"], $this->bgColour["blue"]);
    }
    $pen = imagecolorallocate($img, $this->penColour["red"], $this->penColour["green"], $this->penColour["blue"]);
    imagefill($img, 0, 0, $bg);
    if (is_null($json))
      return false;
    if (is_string($json))
      $json = json_decode(stripslashes($json));
    foreach ($json as $v){
      $this->drawThickLine($img, $v->lx * $this->drawMultiplier, $v->ly * $this->drawMultiplier, $v->mx * $this->drawMultiplier, $v->my * $this->drawMultiplier, $pen, $this->penWidth * ($this->drawMultiplier / 2));
    }
    $imgDest = imagecreatetruecolor($this->width, $this->height);
    if ($this->bgColour == 'transparent') {
      imagealphablending($imgDest, false);
      imagesavealpha($imgDest, true);
    }
    imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $this->width, $this->width, $this->width * $this->drawMultiplier, $this->width * $this->drawMultiplier);
    imagedestroy($img);
    return $imgDest;
  }

  /**
   *  Draws a thick line
   *  Changing the thickness of a line using imagesetthickness doesn't produce as nice of result
   *
   *  @param object $img
   *  @param int $startX
   *  @param int $startY
   *  @param int $endX
   *  @param int $endY
   *  @param object $colour
   *  @param int $thickness
   *
   *  @return void
   */
  protected function drawThickLine ($img, $startX, $startY, $endX, $endY, $colour, $thickness) {
    $angle = (atan2(($startY - $endY), ($endX - $startX)));

    $dist_x = $thickness * (sin($angle));
    $dist_y = $thickness * (cos($angle));

    $p1x = ceil(($startX + $dist_x));
    $p1y = ceil(($startY + $dist_y));
    $p2x = ceil(($endX + $dist_x));
    $p2y = ceil(($endY + $dist_y));
    $p3x = ceil(($endX - $dist_x));
    $p3y = ceil(($endY - $dist_y));
    $p4x = ceil(($startX - $dist_x));
    $p4y = ceil(($startY - $dist_y));

    $array = array(0=>$p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
    imagefilledpolygon($img, $array, (count($array)/2), $colour);
  }

} 