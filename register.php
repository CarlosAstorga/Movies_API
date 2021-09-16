<?php


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errorBag               = [];
    $name                   = $_POST['name'];
    $email                  = $_POST['email'];
    $password               = $_POST['password'];
    $confirmPassword        = $_POST['confirmPassword'];

    if (empty($name)) $errorBag['name']                         = 'Name field required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errorBag['email']                                      = 'Invalid email format';
    if (empty($password)) $errorBag['password']                 = 'Password field required';
    if (empty($confirmPassword)) $errorBag['confirmPassword']   = 'Password confirmation field required';
    if ($password !== $confirmPassword) $errorBag['password']   = "The passwords don't match";

    if (empty($errorBag)) {
        $url            = "";
        $credentials    = [
            'Name'              => $name,
            'User'              => $email,
            'Password'          => $password,
            'confirmPassword'   => $confirmPassword
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
            <h1>Sign up</h1>
            <?php if (!empty($result) && $result['Response'] == 'True') : ?>
                <span class="alert success">
                    <?php echo $result['Message'] ?>
                </span>
            <?php endif ?>

            <?php if (!empty($result) && $result['Response'] == 'False') : ?>
                <span class="alert warning" role="alert">
                    <?php echo $result['Error'] ?>
                </span>
            <?php endif ?>
        </header>

        <form method="POST" action="register">
            <input type="text" name="name" placeholder="Name" value="<?php if (!empty($name) && empty($result['Message'])) echo $name ?>" required>
            <?php if (!empty($errorBag['name'])) : ?> <span class="invalid-feedback"><?php echo $errorBag['name'] ?></span><?php endif ?>
            <input type="email" name="email" placeholder="user@email.com" value="<?php if (!empty($email) && empty($result['Message'])) echo $email ?>" required>
            <?php if (!empty($errorBag['email'])) : ?> <span class="invalid-feedback"><?php echo $errorBag['email'] ?></span><?php endif ?>
            <input type="password" name="password" placeholder="password" required>
            <?php if (!empty($errorBag['password'])) : ?> <span class="invalid-feedback"><?php echo $errorBag['password'] ?></span><?php endif ?>
            <input type="password" name="confirmPassword" placeholder="confirm password" required>
            <?php if (!empty($errorBag['confirmPassword'])) : ?> <span class="invalid-feedback"><?php echo $errorBag['confirmPassword'] ?></span><?php endif ?>

            <!-- Button -->
            <button>Sign up</button>
        </form>

        <footer class="flex-column-center">
            <p>Returning user? <a href="#">Login</a></p>
        </footer>
    </main>
</body>

</html>