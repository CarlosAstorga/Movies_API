<?php


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errorBag   = [];
    $user       = $_POST['email'];
    $password   = $_POST['password'];

    if (!filter_var($user, FILTER_VALIDATE_EMAIL))
        $errorBag['user'] = 'Invalid email format';

    if (empty($password)) $errorBag['password'] = 'Password field required';

    if (empty($errorBag)) {
        $url            = "";
        $credentials    = [
            'User'      => $user,
            'Password'  => $password
        ];

        $resource       = curl_init();
        curl_setopt_array($resource, [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => true,
            CURLOPT_HTTPHEADER      => ['content-type: application/json'],
            CURLOPT_POSTFIELDS      => json_encode($credentials)
        ]);

        $result         = curl_exec($resource);
        curl_close($resource);
        $result         = json_decode($result, true);

        if ($result['Response']     == "True") {
            session_start();
            $_SESSION["Token"]      = $result['Token'];
            header('Location: ');
            exit;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movies API</title>
    <link href="assets/css/login.css" rel="stylesheet">
</head>

<body>
    <main>
        <header>
            <h1>Login</h1>
        </header>

        <form method="POST" action="login">
            <!-- User -->
            <input id="email" type="email" name="email" <?php if (!empty($errorBag['user'])) : ?> class="is-invalid" <?php endif ?> placeholder="user@email.com" required />
            <?php if (!empty($errorBag['user'])) : ?> <span class="invalid-feedback"><?php echo $errorBag['user'] ?></span><?php endif ?>

            <!-- Password -->
            <input type="password" name="password" <?php if (!empty($errorBag['password'])) : ?> class="is-invalid" <?php endif ?> placeholder="Password" required />
            <?php if (!empty($errorBag['password'])) : ?> <span class="invalid-feedback" role="alert"><?php echo $errorBag['password'] ?></span><?php endif ?>

            <?php if (!empty($result) && $result['Response'] == 'False') : ?>
                <span class="invalid-feedback" role="alert">
                    <?php echo $result['Error'] ?>
                </span>
            <?php endif ?>

            <!-- Button -->
            <button>Login</button>
        </form>

        <footer class="flex-column-center">
            <p>No account? <a href="#">Sign up</a></p>
        </footer>
    </main>
</body>

</html>