<?php

$settings = $_POST;
$file = $_FILES["file"];

$bytes = $file["size"];
$megabytes = $bytes / 1048576;

if ($megabytes > 5) {
  echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>File can't be more than 5Mb!</div>";
  exit;
} else {

ini_set('memory_limit', '-1');
if (@$size = getimagesize($file["tmp_name"])) {
  if ($size["mime"] == "image/jpeg") {
    $image = imagecreatefromjpeg($file["tmp_name"]);
  } else if ($size["mime"] == "image/png") {
    $image = imagecreatefrompng($file["tmp_name"]);
  }
  $width = $size[0];
  $height = $size[1];
  $finalimage = imagecreatetruecolor($width, $height);
  $rgb = json_decode($settings["background"]);
  if (isset($rgb[3])) {
  $bg = imagecolorallocatealpha($finalimage, $rgb[0], $rgb[1], $rgb[2], ($rgb[3] * 127)); //alpha is a number from 0-127, rgb[3] is a decimal between 0 and 1 (or 0/1)
  } else {
  $bg = imagecolorallocate($finalimage, $rgb[0], $rgb[1], $rgb[2]);
  }
  imagefill($finalimage, 0, 0, $bg);
  for ($y = 1; $height >= $y; $y+=$settings["yspacing"]) {
    for ($x = 1; $width >= $x; $x+=$settings["xspacing"]) {

      if (isset($settings["minsize"])) {
        $size = rand($settings["minsize"], $settings["maxsize"]);
      } else {
        $size = $settings["size"];
      }

      if ($settings["xoffset"] !== 0) {
        $offsetx = rand(0, $settings["xoffset"]);
        $half = $settings["xoffset"] / 2;

        if ($offsetx > $half) {
          $middlex = $x - $offsetx - $settings["xoffset"];
        } else {
          $middlex = $x - $offsetx;
        }

        if ($middlex < 0) {
          $middlex = 1;
        }

        if ($middlex >= $width) {
          $middlex = $width;
        }

        if ($middlex < ($size / 2)) {
          $topleftx = 0;
        } else {
          $topleftx = $middlex - ($size / 2);
        }

        if ($middlex > ($width - ($size / 2))) {
          $bottomrightx = $width;
        } else {
          $bottomrightx = $middlex + ($size / 2);
        }

      } else {
        $middlex = $x;
      }

      if ($settings["yoffset"] !== 0) {
        $offsety = rand(0, $settings["yoffset"]);
        $half = $settings["yoffset"] / 2;

        if ($offsety > $half) {
          $middley = $y - $offsety - $settings["yoffset"];
        } else {
          $middley = $y - $offsety;
        }

        if ($middley < 0) {
          $middley = 1;
        }

        if ($middley >= $height) {
          $middley = $height;
        }

        if ($middley < ($size / 2)) {
          $toplefty = 0;
        } else {
          $toplefty = $middley - ($size / 2);
        }

        if ($middley > ($height - ($size / 2))) {
          $bottomrighty = $height;
        } else {
          $bottomrighty = $middley + ($size / 2);
        }

      } else {
        $middley = $y;
      }

      if ($settings["colorfrom"] == "original") {
        $index = imagecolorat($image, $x , $y);
      } else {
        $index = imagecolorat($image, $middlex , $middley);
      }
      $color = imagecolorsforindex($finalimage, $index);
      $shapeColor = imagecolorallocatealpha($finalimage, $color["red"], $color["green"], $color["blue"], $color["alpha"]);


      if ($settings["shape"] == "mixed") {
        $allshapes = json_decode($settings["enabledshapes"]);
        $shape = $allshapes[array_rand($allshapes)];
      } else {
        $shape = $settings["shape"];
      }

      if ($shape == "square") {
        imagefilledrectangle($finalimage, $topleftx, $toplefty, $bottomrightx, $bottomrighty, $shapeColor);
      } else if ($shape == "circle") {
        imagefilledellipse($finalimage, $middlex, $middley, $size, $size, $shapeColor);
      } else if ($shape == "triangle") {
        if ($settings["triangleposition"] == "random") {
          $triangleposition = rand(0, 11);
        } else {
          $triangleposition = $settings["triangleposition"];
        }
        $vertices = triangleVertices($triangleposition, $middlex, $middley, $size);
        imagefilledpolygon($finalimage, $vertices, 3, $shapeColor);
      } else {
        echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error! Unknown shape type: " . $shape . "</div>";
        exit;
      }
    }
  }

  // output the picture
  imagesavealpha($finalimage, TRUE);
  //imagepng($finalimage, "img/temp/" . $settings["filename"] . ".png");

  ob_start ();

  imagepng ($finalimage);
  $image_data = ob_get_contents ();

  ob_end_clean ();

  $base64 = base64_encode ($image_data);

  echo "<h2>Result</h2><img class='finalimage' src='data:image/png;base64," . $base64 . "' /><a href='data:image/png;base64," . $base64 . "' download='your_result.png'><br /><button type='button' class='btn btn-success download-button'>Download</button></a>";

} else {
  echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Not an image! Only .png and .jpeg allowed!</div>";
  exit;
}
}

//function triangleVertices creates an array with triangle vertices
//param:
//      $position = number 0-11, triangle position clockwise
//                  top of the triangle:
//                  0 = ↑ (top)
//                  1 = ↗ (top right short version)
//                  2 = ↗ (top right long version)
//                  3 = → (right)
//                  4 = ↘ (bottom right short version)
//                  5 = ↘ (bottom right long version)
//                  6 = ↓ (bottom)
//                  7 = ↙ (bottom left short version)
//                  8 = ↙ (bottom left long version)
//                  9 = ← (left)
//                  10 = ↖ (top left short version)
//                  11 = ↖ (top left long version)
//      $x = current MIDDLE x position
//      $y = current MIDDLE y position
//      $size = triangle size
//      $width = width of the image!
//      $height = height of the image!

function triangleVertices($position, $x, $y, $size, $width = 0, $height = 0) {
  $half = $size / 2;

  $top = array("x" => $x, "y" => ($y - $half));
  $topright = array("x" => ($x + $half), "y" => ($y - $half));
  $right = array("x" => ($x + $half), "y" => $y);
  $bottomright = array("x" => ($x + $half), "y" => ($y + $half));
  $bottom = array("x" => $x, "y" => ($y + $half));
  $bottomleft = array("x" => ($x - $half), "y" => ($y + $half));
  $left = array("x" => ($x - $half), "y" => $y);
  $topleft = array("x" => ($x - $half), "y" => ($y - $half));

  //TO DO:
  //add another triangle version see:
  //xhbvmzx5yh.png  short version
  //and
  //zb7msmb1j0.png  tall version
  switch($position) {
    case 0: //top
      $vertices = array(
        $top["x"],            $top["y"],
        $bottomleft["x"],     $bottomleft["y"],
        $bottomright["x"],    $bottomright["y"]
      );
      break;
    case 1: //top right short version
      $vertices = array(
        $topright["x"],       $topright["y"],
        $bottomright["x"],    $bottomright["y"],
        $topleft["x"],        $topleft["y"]
      );
      break;
    case 2: //top right long version
      $vertices = array(
        $topright["x"],       $topright["y"],
        $bottom["x"],         $bottom["y"],
        $left["x"],           $left["y"]
      );
      break;
    case 3: //right
      $vertices = array(
        $right["x"],          $right["y"],
        $topleft["x"],        $topleft["y"],
        $bottomleft["x"],     $bottomleft["y"]
      );
      break;
    case 4: //bottom right short version
      $vertices = array(
        $bottomright["x"],    $bottomright["y"],
        $topright["x"],       $topright["y"],
        $bottomleft["x"],     $bottomleft["y"]
      );
      break;
    case 5: //bottom right long version
      $vertices = array(
        $bottomright["x"],    $bottomright["y"],
        $top["x"],            $top["y"],
        $left["x"],           $left["y"]
      );
      break;
    case 6: //bottom
      $vertices = array(
        $bottom["x"],         $bottom["y"],
        $topleft["x"],        $topleft["y"],
        $topright["x"],       $topright["y"]
      );
      break;
    case 7: //bottom left short version
      $vertices = array(
        $bottomleft["x"],     $bottomleft["y"],
        $topleft["x"],        $topleft["y"],
        $bottomright["x"],    $bottomright["y"]
      );
      break;
    case 8: //bottom left long version
      $vertices = array(
        $bottomleft["x"],     $bottomleft["y"],
        $top["x"],            $top["y"],
        $right["x"],          $right["y"]
      );
      break;
    case 9: //left
      $vertices = array(
        $left["x"],           $left["y"],
        $topright["x"],       $topright["y"],
        $bottomright["x"],    $bottomright["y"]
      );
      break;
    case 10: //top left short version
      $vertices = array(
        $topleft["x"],        $topleft["y"],
        $topright["x"],       $topright["y"],
        $bottomleft["x"],     $bottomleft["y"]
      );
      break;
    case 11: //top left long version
      $vertices = array(
        $topleft["x"],        $topleft["y"],
        $right["x"],          $right["y"],
        $bottom["x"],         $bottom["y"]
      );
      break;
    default:
      echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error! Triangle position not defined or unknown position type!</div>";
      exit;
  }
  return $vertices;
}
?>
