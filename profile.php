<?php
    require_once 'header.php';

    if (!$loggedin) die("</div></body></html>");

    echo "<h3>Your Profile</h3>";
    //about me upload
    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if (isset($_POST['text']))
    {
        $text = sanitizeString($_POST['text']);
        $text = preg_replace('/\s\s+/', ' ', $text);

        if ($result->rowCount())
            queryMysql("UPDATE profiles SET text='$text' where user='$user'");
        else queryMysql("INSERT INTO profiles VALUES('$user', '$text')");
    }
    else 
    {
        if($result->rowCount())
        {
            $row = $result->fetch();
            $text = stripslashes($row['text']);
        }
        else $text = "";
    }
    $text = stripslashes(preg_replace('/\s\s+/', ' ', $text));

    //image upload
    if (isset($_FILES['image']['name']))
    {
        $saveto = "$user.jpg";
        move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
        $typeok = TRUE;

        switch($_FILES['image']['type'])
        {
            case "image/gif": $src = imagecreatefromgif($saveto); break;
            case "image/jpeg": 
            case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
            case "image/png": $src = imagecreatefrompng($saveto); break;
            default: $typeok = FALSE; break;
        }
        if ($typeok)
        {
            //$w will be saved to the first item in the array and $h wil lbe the second. Like destructuring
            list($w, $h) = getimagesize($saveto);

            $max = 100;
            $tw = $w;
            $th = $h;
            
            if ($w > $h && $max < $w)
            {
                $th = max / $h * $w;
                $max = $max;
            } 
            elseif ($h > $w && $max < $h)  
            {
                $tw = $max / $h * $w;
                $th = $max;
            }
            elseif ($max < $w)
            {
                $tw = $th = $max;
            }

            $tmp = imagecreatetruecolor($tw, $th);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);

            imageconvolution($tmp, array(array(-1, -1, -1), 
                array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
            imagejpeg($tmp, $saveto);
            imagedestroyed($tmp);
            imagedestroy($src);
        }
    }
    showProfile($user);

    echo <<<_END
        <form data-ajax='false' method='post'
            action='profile.php?r=$randstr' encytpe='multipart/form-data'>
        <h3>Enter or edit your details and/or upload an image</h3>
        <textarea name='text'>$text</textarea><br>
        Image: <input type='file' name='image' size='14'>
        <input type='submit' value='Save Profile'>
    _END;
?>