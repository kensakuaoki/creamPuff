<?php
session_start();
if (empty($_SESSION['id_token'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/aws.php';
use Aws\S3\S3Client;
$client = new S3Client([
    'region' => $config['region'],
    'version' => $config['version'],
    'credentials' => $config['credentials'],
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'productCode' => $_POST['productCode'] ?? '',
        'expiryDate' => $_POST['expiryDate'] ?? '',
        'lotNumber' => $_POST['lotNumber'] ?? '',
        'institutionCode' => $_POST['institutionCode'] ?? '',
        'hospital' => $_POST['hospital'] ?? '',
        'timestamp' => time(),
    ];
    $objectKey = 'entries/' . $data['timestamp'] . '.json';
    try {
        $client->putObject([
            'Bucket' => $config['s3_bucket'],
            'Key'    => $objectKey,
            'Body'   => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);
        echo 'Saved to S3';
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error: ' . $e->getMessage();
    }
    return;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GS1 DataBar Scanner</title>
  <script src="https://unpkg.com/@zxing/library@latest"></script>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h1>GS1 DataBar Scanner</h1>
  <video id="video-preview"></video>
  <div id="status"></div>
  <form id="info-form" method="post">
    <div class="form-row">
      <label for="product-code">商品コード(AI=01)</label>
      <input type="text" id="product-code" name="productCode" readonly />
    </div>
    <div class="form-row">
      <label for="expiry-date">有効期限(YYMMDD, AI=17)</label>
      <input type="text" id="expiry-date" name="expiryDate" readonly />
    </div>
    <div class="form-row">
      <label for="lot-number">ロット番号(AI=10)</label>
      <input type="text" id="lot-number" name="lotNumber" readonly />
    </div>
    <div class="form-row">
      <label for="institution-code">医療機関コード</label>
      <input type="text" id="institution-code" name="institutionCode" required />
    </div>
    <div class="form-row">
      <label for="hospital">医療機関名</label>
      <input type="text" id="hospital" name="hospital" required />
    </div>
    <button type="submit" class="btn" id="send-mail">送信</button>
    <button type="button" class="btn" id="rescan">再スキャン</button>
  </form>
  <button id="start-scan" class="btn">スキャン開始</button>
  <script src="js/app.js"></script>
</body>
</html>
