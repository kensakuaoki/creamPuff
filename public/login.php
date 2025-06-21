<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/aws.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

$client = new CognitoIdentityProviderClient([
    'region' => $config['region'],
    'version' => $config['version'],
    'credentials' => $config['credentials'],
]);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = $client->initiateAuth([
            'AuthFlow' => 'USER_PASSWORD_AUTH',
            'ClientId' => $config['cognito']['client_id'],
            'AuthParameters' => [
                'USERNAME' => $_POST['username'] ?? '',
                'PASSWORD' => $_POST['password'] ?? '',
            ],
        ]);
        $_SESSION['id_token'] = $result['AuthenticationResult']['IdToken'];
        header('Location: form.php');
        exit;
    } catch (Exception $e) {
        $message = 'Login failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>Login</h1>
<?php if ($message) echo '<p>' . htmlspecialchars($message) . '</p>'; ?>
<form method="post">
  <div class="form-row">
    <label>Username<input type="text" name="username" required></label>
  </div>
  <div class="form-row">
    <label>Password<input type="password" name="password" required></label>
  </div>
  <button type="submit" class="btn">Login</button>
</form>
</body>
</html>
