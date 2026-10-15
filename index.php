<?php
// index.php

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["payer"])) {

    // Remplace par tes clés API PayTech
    $api_key = "834b4fc0678803502512e1d22d57ab7af35d93f9c712814da15110505c594eba";
    $api_secret = "7d86bab28fc3ba1f63e5ad68b2fe89d4376d72f074c813190d7d003fa9368b84";

    $data = [
        "item_name"   => "Produit de test",
        "item_price"  => 200,
        "currency"    => "XOF",
        "ref_command" => "TEST-" . time(),
        "command_name"=> "Paiement de test",
        "env"         => "prod",
        
"ipn_url"     => "https://test-pay-0zuw.onrender.com/?ipn=1",
"success_url" => "https://test-pay-0zuw.onrender.com/?state=success",
"cancel_url"  => "https://test-pay-0zuw.onrender.com/?state=cancel"

    ];

    $ch = curl_init(
        "https://paytech.sn/api/payment/request-payment"
    );

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            "API_KEY: " . $api_key,
            "API_SECRET: " . $api_secret,
            "Content-Type: application/json"
        ],
        CURLOPT_TIMEOUT        => 20
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        $message = "Erreur de connexion : " . $curl_error;
    } else {
        $result = json_decode($response, true);

        if (
            $http_code >= 200 &&
            $http_code < 300 &&
            isset($result["success"]) &&
            (int)$result["success"] === 1 &&
            !empty($result["redirect_url"])
        ) {
            // Rediriger le client vers PayTech
            header("Location: " . $result["redirect_url"]);
            exit;
        }

        $message = "Échec du paiement. Réponse PayTech : "
            . htmlspecialchars($response, ENT_QUOTES, "UTF-8");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PayTech</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #0002;
            width: 320px;
            text-align: center;
        }

        .prix {
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
        }

        button {
            background: #16a34a;
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .message {
            overflow-wrap: anywhere;
            color: #b91c1c;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Test PayTech</h1>

        <p>Produit de démonstration</p>

        <div class="prix">1 00 FCFA</div>

        <form method="POST">
            <button type="submit" name="payer" value="1">
                Payer maintenant
            </button>
        </form>

        <?php if ($message !== ""): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
