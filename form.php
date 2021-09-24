<?php

session_start();
$errorBag   = [];

if ($_SERVER['REQUEST_METHOD']  == "POST") {
    $title  = validate($_POST['title'])     ?? "";
    $year   = validate($_POST['year'])      ?? "";
    $imdbid = validate($_POST['imdbid'])    ?? "";
    $type   = validate($_POST['type'])      ?? "";
    $plot   = validate($_POST['plot'])      ?? "";
    $poster = $_FILES['poster']['tmp_name'] ? $_FILES['poster'] : "";

    if (!empty($poster)) {
        $fileExt            = explode('/', $poster['type'])[1];
        $isImage            = getimagesize($poster['tmp_name']);
        $allowedExtensions  = ['jpeg', 'jpg', 'png'];

        if ($isImage === false) {
            $errorBag['poster'] = "File is not an image";
        } else if ($poster['size'] > 2000000) {
            $errorBag['poster'] = "File too large";
        } else if (!in_array($fileExt, $allowedExtensions)) {
            $errorBag['poster'] = 'File extension not allowed';
        } else {
            $mime   = $poster['type'];
            $base64 = base64_encode(file_get_contents($poster["tmp_name"]));
            $poster = "data:$mime;base64,$base64";
        }
    }

    $types = ["Movie", "Serie", "Episode"];

    empty($year) ?
        $errorBag['year']       = "Field required"      : (!preg_match("/^[\d\s-]+$/", $year) ?
            $errorBag['year']   = "Only numbers"        : null);

    if (empty($title))  $errorBag['title']  = 'Field required';
    if (empty($type))   $errorBag['type']   = 'Field required';
    if (!empty($imdbid) && !preg_match("/^[\d]+$/", $imdbid))
        $errorBag['imdbid']     = "Only numbers";

    empty($type) ?
        $errorBag['type']       = "Field required"      : (!in_array($type, $types) ?
            $errorBag['type']   = "Option not allowed"  : null);

    if (empty($errorBag)) {
        $url                    = 'https://astorga-api-movies.herokuapp.com/movies';
        $movie                  = [
            'Title'             => $title,
            'Year'              => $year,
            'Type'              => $type,
            'Plot'              => $plot,
            'Token'             => $_SESSION["Token"]
        ];

        if (!empty($imdbid))    $movie['imdbID'] = "tt$imdbid";

        $resource   = curl_init();
        curl_setopt_array($resource, [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => true,
            CURLOPT_HTTPHEADER      => ['content-type: application/json'],
            CURLOPT_POSTFIELDS      => json_encode($movie)
        ]);

        $result     = curl_exec($resource);
        curl_close($resource);
        $result     = json_decode($result, true);
    }
}

function validate($data)
{
    $data           = trim($data);
    $data           = stripslashes($data);
    $data           = htmlspecialchars($data);
    return $data;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Movies API</title>
    <link rel="stylesheet" href="assets/css/form.css" />
</head>

<body>
    <form id="form" method="POST" action="form" enctype="multipart/form-data">
        <h1>New Movie</h1>
        <?php if (!empty($result) && $result['Response'] == 'True') : ?>
            <span class="alert success">
                <?php echo $result['Message'] ?>
                <i id="closeIcon" class="fas fa-times"></i>
            </span>
        <?php endif ?>

        <!-- Title -->
        <div class="input-group">
            <label for="title"><span class="secondary">*</span> Title</label>
            <input id="title" type="text" name="title" maxlength="200" value="<?php if (!empty($errorBag)) echo $title ?>" required />
            <?php if (!empty($errorBag['title'])) : ?> <span class="error"><?php echo $errorBag['title'] ?></span><?php endif ?>
        </div>

        <div class="container grid columns">
            <!-- Year -->
            <div class="input-group">
                <label for="year"><span class="secondary">*</span> Year</label>
                <input id="year" type="text" name="year" minlength="4" maxlength="11" value="<?php if (!empty($errorBag)) echo $year ?>" required />
                <?php if (!empty($errorBag['year'])) : ?> <span class="error"><?php echo $errorBag['year'] ?></span><?php endif ?>
            </div>

            <!-- imdb -->
            <div class="input-group">
                <label for="imdbid">Imdb</label>
                <input id="imdbid" type="text" name="imdbid" placeholder="1285016" inputmode="numeric" minlength="7" maxlength="7" />
                <?php if (!empty($errorBag['imdbid'])) : ?> <span class="error"><?php echo $errorBag['imdbid'] ?></span><?php endif ?>
            </div>

            <!-- Type -->
            <div class="input-group">
                <label for="type">Type</label>
                <select name="type" id="type">
                    <option value="Movie">Movie</option>
                    <option value="Serie">Serie</option>
                    <option value="Episode">Episode</option>
                </select>
                <?php if (!empty($errorBag['type'])) : ?> <span class="error"><?php echo $errorBag['type'] ?></span><?php endif ?>
            </div>

            <!-- Poster -->
            <div class="input-group">
                <small><span class="secondary">Max size 2MB</span></small>
                <label for="fileInput">
                    <i class="fas fa-file-upload"></i>
                    <span class="file-name"> Upload poster</span>
                </label>
                <input type="file" name="poster" id="fileInput" accept=".jpg,.jpeg,.png">
                <?php if (!empty($errorBag['poster'])) : ?> <span class="error"><?php echo $errorBag['poster'] ?></span><?php endif ?>
            </div>
        </div>

        <!-- Plot -->
        <div class="input-group">
            <label for="Plot">Plot</label>
            <textarea name="plot" id="plot" rows="6" maxlength="200"><?php if (!empty($errorBag)) echo $plot ?></textarea>
        </div>

        <button class="btn primary" type="submit">Create</button>
    </form>
    <script src="https://kit.fontawesome.com/f28e386e36.js" crossorigin="anonymous"></script>
    <script src="assets/js/form.js" defer></script>
</body>

</html>