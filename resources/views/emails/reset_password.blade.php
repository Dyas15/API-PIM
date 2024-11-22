<!DOCTYPE html>
<html>
<head>
    <title>Redefinição de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .header img {
            max-width: 150px;
        }
        .header h1 {
            margin: 10px 0;
            color: rgba(4, 56, 63, 1);
        }
        .content {
            padding: 20px 0;
            text-align: center;
        }
        .content p {
            margin: 10px 0;
        }
        .content a {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            color: white;
            background-color: rgba(4, 56, 63, 1);
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .content a:hover {
            background-color: #002932;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
        .footer p {
            margin: 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(base_path('public/images/logo.png')) }}" alt="Logo">
            <h1>Redefinição de Senha</h1>
        </div>
        <div class="content">
            <p>Você solicitou uma redefinição de senha. Clique no link abaixo para redefinir sua senha:</p>
            <p><a href="http://localhost:8000/api/redefinicao/{{$token}}">Redefinir Senha</a></p>
            <p>Se você não solicitou a redefinição de senha, ignore este e-mail.</p>
        </div>
        <div class="footer">
            <p>Obrigado por usar nosso serviço!</p>
        </div>
    </div>
</body>
</html>
