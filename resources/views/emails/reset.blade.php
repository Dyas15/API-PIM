<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
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
        .input {
            margin-bottom: 15px;
            text-align: left;
        }
        .input label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .input input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 15px;
            background-color: rgba(4, 56, 63, 1);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #002932;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        .footer .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }
        .footer .logo img {
            max-width: 100px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ url('/') }}/images/logo.png" alt="Logo">
            <h1>Redefinição de Senha</h1>
        </div>
        <form action="resetar" method="POST">
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="input">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" required>
            </div>
            <div class="input">
                <label for="senha">Nova Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="input">
                <label for="confirmação">Confirme a Nova Senha</label>
                <input type="password" id="confirmação" name="confirmação" required>
            </div>
            <button type="submit">Redefinir Senha</button>
        </form>
    </div>
</body>
</html>