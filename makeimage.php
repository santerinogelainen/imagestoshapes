<?php

if (!isset($_POST)) {
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error! Post not set!</div>";
  exit;
}

if (!isset($_FILES)) {
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error! File not set!</div>";
  exit;
}

//see http://php.net/manual/en/features.file-upload.errors.php
switch($_FILES["file"]["error"]) {
case 1:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>The uploaded file exceeds the max upload size set by the server (" . ini_get('upload_max_filesize') . ").</div>";
  exit;
case 2:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>The uploaded file exceeds the max upload size set by the HTML form.</div>";
  exit;
case 3:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>The uploaded file was only partially uploaded.</div>";
  exit;
case 4:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>No file was uploaded.</div>";
  exit;
case 6:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Missing a temporary folder. I don't know how this happened. Please contact me santeri.nogelainen@gmail.com.</div>";
  exit;
case 7:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to write file to disk. Probably something to do with folder permissions. Please contact me santeri.nogelainen@gmail.com.</div>";
  exit;
case 8:
  echo "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>A PHP extension stopped the file upload.</div>";
  exit;
default:
//do nothing
  break;
}

//read post and file
$settings = $_POST;
$file = $_FILES["file"];

//this is here just to make sure
//convert file byte amount to megabytes
$bytes = $file["size"];
$megabytes = $bytes / 1048576;

//we don't want some dumb fuck to overload our server by uploading a 1tb file
if ($megabytes > 5) {
  echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>File can't be more than 5Mb!</div>";
  exit;
} else {

//scary but w/e
ini_set('memory_limit', '-1');

//check that it is indeed an image
if (@$size = getimagesize($file["tmp_name"])) {

  //again we don't want someone to overload the server by uploading a 100000x100000 image with 1x1 spacing between shapes
  if ($size[0] >  5000 || $size[1] > 5000) {
    echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Image height/width can't go over 5000px! Please crop, resample or resize your image.</div>";
    exit;
  }

  //load the image to a PHP variable
  if ($size["mime"] == "image/jpeg") {
    $image = imagecreatefromjpeg($file["tmp_name"]);
  } else if ($size["mime"] == "image/png") {
    $image = imagecreatefrompng($file["tmp_name"]);
  } else {
    echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Only .png and .jpg allowed!</div>";
    exit;
  }

  //easier to remember and read
  $width = $size[0];
  $height = $size[1];

  //this is going to be the result image
  $finalimage = imagecreatetruecolor($width, $height);

  //get the rgb(a) value of the background
  //json decode because javascript formdata does not accept arrays, which means i had to encode it to a string, dirty technique
  $rgb = json_decode($settings["background"]);

  //now we check if alpha is set in the rgb values and allocate the colors
  if (isset($rgb[3])) {
  $bg = imagecolorallocatealpha($finalimage, $rgb[0], $rgb[1], $rgb[2], ($rgb[3] * 127)); //alpha is a number from 0-127, rgb[3] is a decimal between 0 and 1 (or 0/1)
  } else {
  $bg = imagecolorallocate($finalimage, $rgb[0], $rgb[1], $rgb[2]);
  }

  //fill the result image with the background color
  imagefill($finalimage, 0, 0, $bg);

  //loop through the pixels vertically
  for ($y = 1; ($height - 1) > $y; $y+=$settings["yspacing"]) {
    //then horizontally (it is like reading a book, move from left to right then down)
    for ($x = 1; ($width - 1) > $x; $x+=$settings["xspacing"]) {

      //get the size of the shape
      if (isset($settings["minsize"])) {
        $size = rand($settings["minsize"], $settings["maxsize"]);
      } else {
        $size = $settings["size"];
      }

      //oh boy
      //$settings["offset"] = max possible offset
      //if user has set offset to the shape position
      if ($settings["xoffset"] !== 0) {

        //randomize offset
        $offsetx = rand(0, $settings["xoffset"]);

        //half of the max possible offset
        $half = $settings["xoffset"] / 2;

        //if the random number is more than half of the max possible offset
        //this is because we want the offset to either be negative of positive
        //rand() function is kinda shit because you cant do stuff like between -10 and 10
        //without it looking like a spaghetti mess
        //middlex is the x position in the middle of the shape
        if ($offsetx > $half) {
          $middlex = $x + $offsetx - $settings["xoffset"]; //negative offset
        } else {
          $middlex = $x + $offsetx; //positive offset
        }

      } else {
        //if offset is 0 by the user
        $middlex = $x;
      }

      //same thing as previously but for y
      if ($settings["yoffset"] !== 0) {
        $offsety = rand(0, $settings["yoffset"]);
        $half = $settings["yoffset"] / 2;

        if ($offsety > $half) {
          $middley = $y + $offsety - $settings["yoffset"];
        } else {
          $middley = $y + $offsety;
        }

      } else {
        $middley = $y;
      }

      //now we check the place we pick the color from
      if ($settings["colorfrom"] == "original") {
        $index = imagecolorat($image, $x , $y); //from the original position
      } else {
        $index = imagecolorat($image, $middlex , $middley); //from the position after offset
      }

      //get the rbg(a) value of the picked color
      $color = imagecolorsforindex($finalimage, $index);

      //allocate it
      $shapeColor = imagecolorallocatealpha($finalimage, $color["red"], $color["green"], $color["blue"], $color["alpha"]);


      //randomize shape if mixed is selected
      if ($settings["shape"] == "mixed") {
        $allshapes = json_decode($settings["enabledshapes"]);
        $shape = $allshapes[array_rand($allshapes)];
      } else {
        $shape = $settings["shape"];
      }

      //if shape is square
      if ($shape == "square") {
        //count top left aand bottom right from the middle position
        //x
        $topleftx = $middlex - ($size / 2);
        $bottomrightx = $middlex + ($size / 2);
        //y
        $toplefty = $middley - ($size / 2);
        $bottomrighty = $middley + ($size / 2);

        //draw the square
        imagefilledrectangle($finalimage, $topleftx, $toplefty, $bottomrightx, $bottomrighty, $shapeColor);

      } else if ($shape == "circle") {

        //draw the circle
        imagefilledellipse($finalimage, $middlex, $middley, $size, $size, $shapeColor);

      } else if ($shape == "triangle") {

        //randomize triangle position
        if ($settings["triangleposition"] == "random") {
          $triangleposition = rand(0, 11);
        } else {
          $triangleposition = $settings["triangleposition"];
        }

        //get the vertices of the triangle
        $vertices = triangleVertices($triangleposition, $middlex, $middley, $size);

        //draw the triangle
        imagefilledpolygon($finalimage, $vertices, 3, $shapeColor);
      } else {
        echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error! Unknown shape type: " . $shape . "</div>";
        exit;
      }
    }
  }

  //tell php that the image contains an alpha channel
  imagesavealpha($finalimage, TRUE);

  //a wierd way to turn the image to base 64 without saving it
  ob_start ();
  imagepng ($finalimage);
  $image_data = ob_get_contents ();
  ob_end_clean ();
  $base64 = base64_encode ($image_data);

  //display the final image
  echo "<div class='alert alert-info'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><h2>Result</h2><img class='finalimage' src='data:image/png;base64," . $base64 . "' /><a href='data:image/png;base64," . $base64 . "' download='your_result.png'><br /><button type='button' class='btn btn-success download-button'>Download</button></a></div>";

} else {
  echo "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Not an image!</div>";
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
