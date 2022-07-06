<form action="<?php echo $_server['php-self'];  ?>" method="post" enctype="multipart/form-data" id="something" class="uniForm">
        <input name="new_image" id="new_image" size="30" type="file" class="fileUpload" />
        <button name="submit" type="submit" class="submitButton">Upload/Resize Image</button>
</form>
<?php
        if(isset($_POST['submit'])){
          if (isset ($_FILES['new_image'])){
              $imagename = $_FILES['new_image']['name'];
              $source = $_FILES['new_image']['tmp_name'];
              $target = "avatar/".$imagename;
              move_uploaded_file($source, $target);
 
              $imagepath = $imagename;
              $save = "avatar/" . $imagepath; //This is the new file you saving
              $file = "avatar/" . $imagepath; //This is the original file
 
              list($width, $height) = getimagesize($file) ; 
 
              $modwidth = 150; 
 
              $diff = $width / $modwidth;
 
              $modheight = $height / $diff; 
              $tn = imagecreatetruecolor($modwidth, $modheight) ; 
              $image = imagecreatefromjpeg($file) ; 
              imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ; 
 
              imagejpeg($tn, $save, 100) ; 
 
              $save = "avatar/sml_" . $imagepath; //This is the new file you saving
              $file = "avatar/" . $imagepath; //This is the original file
 
              list($width, $height) = getimagesize($file) ; 
 
              $modwidth = 80; 
 
              $diff = $width / $modwidth;
 
              $modheight = $height / $diff; 
              $tn = imagecreatetruecolor($modwidth, $modheight) ; 
              $image = imagecreatefromjpeg($file) ; 
              imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ; 
 
              imagejpeg($tn, $save, 100) ; 
            echo "Large image: <img src='avatar/".$imagepath."'><br>"; 
            echo "Thumbnail: <img src='avatar/sml_".$imagepath."'>"; 
 
          }
        }
?>
