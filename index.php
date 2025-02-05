<?php require_once("includes/header.php"); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gekke Design</title>
    <style>
        body {
            background: linear-gradient(45deg, #ff0099, #493240);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            color: white;
        }
        .crazy-button {
            font-size: 2rem;
            padding: 15px 30px;
            background: #ffcc00;
            border: 3px dashed #000;
            border-radius: 15px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease-in-out;
            animation: shake 0.5s infinite alternate;
        }
        .crazy-button:hover {
            transform: rotate(10deg) scale(1.1);
        }
        @keyframes shake {
            from { transform: rotate(-5deg); }
            to { transform: rotate(5deg); }
        }
    </style>
</head>
<body>
<a class="crazy-button" href="login.php">🚀 Ga naar Login 🚀</a>
</body>
</html>
