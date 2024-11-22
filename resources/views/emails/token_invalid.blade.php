<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Invalido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .header h1 {
            margin: 10px 0;
            color: rgba(4, 56, 63, 1);
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
        }
        a {
            display: inline-block;
            padding: 10px 20px;
            background-color: rgba(4, 56, 63, 1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        a:hover {
            background-color: #002932;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ url('/') }}/images/logo.png" alt="Logo">
            <h1>Seu token é inválido!</h1>
        </div>
        <div class="message">
            Por algum motivo, o token inserido é invalido! <br>
            Por favor, solicite novamente a redefinição de senha para receber outro token.
        </div>
    </div>
</body>
</html>
