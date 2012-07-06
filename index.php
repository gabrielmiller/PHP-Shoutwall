<?php

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" lang="nl">
<head>
    <link rel="icon" href="favicon.ico">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>A Super Duper PHP Shoutwall</title>
    <link rel="stylesheet" href="style.css" type="text/css">
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script> -->    
</head>
<body>
<div id="header">
    <h1>Shoutwall</h1>
</div>
<div id="wrapper">
<?php

function dbConnect()
{
    $connection = new mysqli('localhost', 'user', 'password', 'ex11') or die ('Cannot connect to database');
    return $connection;
}

$sqlselect = 'SELECT pubdate, author, title, text FROM posts ORDER BY pk DESC';
$connection = dbConnect();
$stmt_select = $connection->prepare($sqlselect);
$stmt_select->execute(); 
$stmt_select->bind_result($post_date, $post_author, $post_title, $post_text);

$posts = "<div id=\"posts\">";


while ($stmt_select->fetch()) 
{
$posts .= "<article><header><h4>$post_title</h4>
            <br><em>Posted by $post_author on ".date("Y-m-d g:i A", strtotime($post_date)).'</em></header>
            <div class="article"><p>'.$post_text.'</p></div></article>';
}

$posts .= "</div>";

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    echo '<form id="addpost" action="" method=POST>
    <p><label>Name:</label><input type="text" name="postername" id="postername"></p>
    <p><label>Title:</label><input type="text" name="posttitle" id="posttitle"></p>
    <p><label>Text:</label><textarea name="posttext" id="posttext"></textarea></p>
    <div id="buttongroup">
    <button type="submit" value="send">Submit post</button>
    <button id="reset" type="reset" value="reset">Reset</button>
    </div>
    </form>
    <br>';

    echo $posts;

}

else
{

    $errors = array();

    if(isset($_POST['postername']))
    {
        if(strlen($_POST['postername'])<3)
        {
            if(strlen($_POST['postername']) != 0)
            {
                $errors[] = 'Your post name must be either blank(anonymous) or longer than 3 characters.';
            }
            else
            {
                $_POST['postername'] = "anonymous";
            }
        }
        elseif(strlen($_POST['postername'])>200)
        {
            $errors[] = 'Your post name may not exceed 200 characters.';
        }
    }
    
    if(isset($_POST['posttitle']))
    {
        if(strlen($_POST['posttitle'])<3)
        {
            $errors[] = 'Your post title must be at least 3 characters.';
        }
        elseif(strlen($_POST['posttitle'])>200)
        {
            $errors[] = 'Your post title may not exceed 200 characters.';
        }
    }
    else
    {
        $errors[] = 'Your post must have a title.';
    }

    if(isset($_POST['posttext']))
    {
        if(strlen($_POST['posttext'])<3)
        {
            $errors[] = 'Your post must be at least 3 characters.';
        }
        elseif(strlen($_POST['posttext'])>600)
        {
            $errors[] = 'Your post may not exceed 600 characters.';
        }
    }
    else
    {
        $errors[] = 'Your post must have text.';
    }

    if(!empty($errors))
    {
        if(count($errors)==1)
        {
            echo 'It looks like you had an error:<br><ul>';
        }
        elseif(count($errors)>1)
        {
            echo 'It looks like you had some errors:<br><ul>';
        }
        foreach($errors as $key => $value)
        {
            echo '<li>'.$value.'</li>';
        }
    echo '</ul>';
    }
    else
    {

        $formusername = $_POST['postername'];
        $formtitle    = $_POST['posttitle'];
        $formtext     = strip_tags($_POST['posttext'],'<p><br><a><pre><em><b>');
        $formdate     = date('Y-m-d H:i:s');
 
        $sqlinsert = 'INSERT INTO posts (pubdate, author, title, text)
                      VALUES(?, ?, ?, ?)';

        $connection = dbConnect();
        $stmt = $connection->stmt_init();
        $stmt = $connection->prepare($sqlinsert);
        $stmt->bind_param('ssss', $formdate, $formusername, $formtitle, $formtext);
        $stmt->execute();
        if ($stmt->affected_rows == 1)
        {
            header('Location: index.php');
        }
        else
        {
            echo '<h2>There was a problem with the database!</h2><br><h3>Try again later or contact the <a href="gmiller2007@gmail.com">administrator</a></h3>';
        }
        echo '<br>';
        //echo print_r($connection);
        echo '<br>';
        //echo $formdate;
        echo '<br>';
        //echo var_dump($connection);
        echo '<br>';
        echo '<br>';
        echo $posts;
    }
}




?>
</div>
<div id="footer"><a href="http://www.cheddarcode.com/g">2012 L Gabriel Miller</a></div>
</body>
</html>
