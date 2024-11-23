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
    <script>
        function validarFormulario(event) {
            const cpf = document.getElementById('cpf').value;
            const senha = document.getElementById('senha').value;
            const confirmacao = document.getElementById('confirmação').value;

            if (!validarCPF(cpf)) {
                alert("CPF inválido.");
                event.preventDefault();
                return;
            }

            if (senha.length <= 5) {
                alert("A senha deve ter mais de 5 caracteres.");
                event.preventDefault();
                return;
            }

            if (senha !== confirmacao) {
                alert("As senhas não coincidem.");
                event.preventDefault();
                return;
            }
        }

        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, ''); 
            if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;

            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) {
                soma += parseInt(cpf.charAt(i - 1)) * (11 - i);
            }
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.charAt(9))) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) {
                soma += parseInt(cpf.charAt(i - 1)) * (12 - i);
            }
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            return resto === parseInt(cpf.charAt(10));
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ url('/') }}/images/logo.png" alt="Logo">
            <h1>Redefinição de Senha</h1>
        </div>
        <form action="resetar" method="POST" onsubmit="validarFormulario(event)">
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="input">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" maxlength="11" minlength="11" required>
            </div>
            <div class="input">
                <label for="senha">Nova Senha</label>
                <input type="password" id="senha" name="senha" minlength="6" required>
            </div>
            <div class="input">
                <label for="confirmação">Confirme a Nova Senha</label>
                <input type="password" id="confirmação" name="confirmação" minlength="6" required>
            </div>
            <button type="submit">Redefinir Senha</button>
        </form>
    </div>
</body>
</html>
