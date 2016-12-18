<!DOCTYPE html>
<html>
  <head>
    <title>Image to Shapes</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-colorpicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-colorpicker.min.js"></script>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
  </head>
  <body>
    <div id="infomodal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Information</h4>
          </div>
          <div class="modal-body">
            <p>TO DO</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <nav class='nav navbar-inverse navbar-fixed-top'>
      <div class='container-fluid'>
        <div class='navbar-header'>
          <a class="navbar-brand" href="/">Santeri Nogelainen</a>
        </div>
        <ul class="nav navbar-nav">
          <li><a href="#" data-toggle="modal" data-target="#infomodal">Information</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a target="_blank" href="https://github.com/santerinogelainen/imagestoshapes"><img class="github" src="img/GitHub_Logo_white.png" /></a></li>
        </ul>
      </div>
    </nav>
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-lg-12 results"><div class="loader"></div></div>
        <form class="col-sm-12 col-lg-12">
          <div class="form-group btn-group shapes" data-toggle="buttons">
            <label class="btn btn-primary active">
              <input type="radio" name="shape" id="circles" value="circle" autocomplete="off" checked="checked" /> Circles
            </label>
            <label class="btn btn-primary">
              <input type="radio" name="shape" id="squares" value="square" autocomplete="off"/> Squares
            </label>
            <label class="btn btn-primary">
              <input type="radio" name="shape" id="triangles" value="triangle" autocomplete="off"/> Triangles
            </label>
            <label class="btn btn-primary">
              <input type="radio" name="shape" id="mixed" value="mixed" autocomplete="off"/> Mixed
            </label>
          </div>
          <div class="form-group file_selection">
            <label for="choose_file">Choose image: </label>
            <input accept="image/jpeg,image/png" name="image" type="file" id="choose_file">
          </div>
          <div class="form-group">
            <label for="cp4">Image background-color: </label>
            <a href="#" class="btn btn-default" id="cp4">Choose Color</a>
          </div>
          <h3>Circle/Triangle/Square settings</h3>
          <div class="form-group btn-group enableshapes" data-toggle="buttons">
            <h4>Enable / disable shapes</h4>
            <label class="btn btn-primary active">
              <input type="checkbox" name="shapesenabled" value="circle" autocomplete="off" checked="checked" /> Circles
            </label>
            <label class="btn btn-primary active">
              <input type="checkbox" name="shapesenabled" value="square" autocomplete="off" checked="checked"/> Squares
            </label>
            <label class="btn btn-primary active">
              <input type="checkbox" name="shapesenabled" value="triangle" class="enabletriangle" autocomplete="off" checked="checked"/> Triangles
            </label>
          </div>
          <div class="form-group triangle_position">
              <h4>Triangle position (choose one)</h4>
              <table>
                <tbody>
                  <tr>
                    <td>
                    <label><input type="radio" id="top-left-long" name="triangle_position" value="11"><img class="position_img" src="img/triangle_shapes/top-left-long.svg" /></label>
                    </td>
                    <td></td>
                    <td>
                    <label><input type="radio" id="top" name="triangle_position" value="0" checked="checked"><img class="position_img" src="img/triangle_shapes/top.svg" /></label>
                    </td>
                    <td></td>
                    <td>
                    <label><input type="radio" id="top-right-long" name="triangle_position" value="2"><img class="position_img" src="img/triangle_shapes/top-right-long.svg" /></label>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td>
                    <label><input type="radio" id="top-left-short" name="triangle_position" value="10"><img class="position_img" src="img/triangle_shapes/top-left-short.svg" /></label>
                    </td>
                    <td></td>
                    <td>
                    <label><input type="radio" id="top-right-short" name="triangle_position" value="1"><img class="position_img" src="img/triangle_shapes/top-right-short.svg" /></label>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>
                    <label><input type="radio" id="left" name="triangle_position" value="9"><img class="position_img" src="img/triangle_shapes/left.svg" /></label>
                    </td>
                    <td colspan="3"><label><input type="radio" id="random" name="triangle_position" value="random"><span class="random_position_header">RANDOM</span></label></td>
                    <td>
                    <label><input type="radio" id="right" name="triangle_position" value="3"><img class="position_img" src="img/triangle_shapes/right.svg" /></label>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td>
                    <label><input type="radio" id="bottom-left-short" name="triangle_position" value="7"><img class="position_img" src="img/triangle_shapes/bottom-left-short.svg" /></label>
                    </td>
                    <td></td>
                    <td>
                    <label><input type="radio" id="bottom-right-short" name="triangle_position" value="4"><img class="position_img" src="img/triangle_shapes/bottom-right-short.svg" /></label>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>
                    <label><input type="radio" id="bottom-left-long" name="triangle_position" value="8"><img class="position_img" src="img/triangle_shapes/bottom-left-long.svg" /></label>
                    </td>
                    <td></td>
                    <td>
                    <label><input type="radio" id="bottom" name="triangle_position" value="6"><img class="position_img" src="img/triangle_shapes/bottom.svg" /></label>
                    </td>
                    <td></td>
                    <td>
                    <label><input type="radio" id="bottom-right-long" name="triangle_position" value="5"><img class="position_img" src="img/triangle_shapes/bottom-right-long.svg" /></label>
                    </td>
                  </tr>
                </tbody>
              </table>
          </div>
          <div class="form-group">
              <h4>Spacing</h4>
              <label for="widthspacing">X Spacing: <span id="xspacing">10</span></label>
              <input type="range" id="widthspacing" name="widthspacing" min="1" max="200" value="10">
              <label for="heightspacing">Y Spacing: <span id="yspacing">10</span></label>
              <input type="range" id="heightspacing" name="heightspacing" min="1" max="200" value="10">
          </div>
          <div class="form-group">
              <h4>Random offset (set to 0 if you don't want any offset)</h4>
              <label for="maxoffsetx">Max offset x: <span id="maxox">0</span></label>
              <input type="range" id="maxoffsetx" name="maxoffsetx" min="0" max="200" value="0">
              <label for="maxoffsety">Max offset y: <span id="maxoy">0</span></label>
              <input type="range" id="maxoffsety" name="maxoffsety" min="0" max="200" value="0">
          </div>
          <div class="form-group">
              <h4>Size</h4>
              Randomize size: <input type="checkbox" class="showhidden" /><br /><br />
              <label class="hiddenrange" for="minsize">Min size: <span id="minsizenumber">10</span></label>
              <input class="hiddenrange" type="range" id="minsize" name="minsize" min="1" max="200" value="10">
              <label class="hiddenrange" for="maxsize">Max size: <span id="maxsizenumber">30</span></label>
              <input class="hiddenrange" type="range" id="maxsize" name="maxsize" min="1" max="200" value="30">
              <label class="shownrange" for="size">Size: <span id="sizenumber">10</span></label>
              <input class="shownrange" type="range" id="size" name="size" min="1" max="200" value="10">
          </div>
          <div class="form-group">
              <h4>Color pick options</h4>
              <label>Pick color from:</label><br /><br />
              Original position: <input type="radio" name="colorfrom" value="original" checked="checked" /><br />
              Final position (after offset, etc.): <input type="radio" name="colorfrom" value="final" />
          </div>
          <div class="form-group">
            <a href="#" class="btn btn-default" id="submit">Generate Image</a>
          </div>
        </form>
      </div>
    </div>
    <script src="js/script.js"></script>
  </body>
</html>