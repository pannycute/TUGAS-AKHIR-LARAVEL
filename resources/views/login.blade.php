<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Lantana Jaya Digital</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #fff5f5;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #ffffff;
            border: 1px solid #f5c2c7;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            color: #c82333;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #721c24;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #f5c2c7;
            border-radius: 5px;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #dc3545;
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.3);
        }

        .btn-submit {
            width: 100%;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #c82333;
        }

        .message {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }

        .message.error {
            color: #c82333;
        }

        .message.success {
            color: #28a745;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login Admin</h2>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email anda" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div id="message" class="message"></div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const messageBox = document.getElementById('message');

            // Clear previous message
            messageBox.innerText = '';
            messageBox.classList.remove('error', 'success');

            axios.post('http://localhost:8000/api/login', {
                email: email,
                password: password
            })
            .then(function (response) {
                if (response.data.success) {
                    const user = response.data.data;
                    messageBox.classList.add('success');
                    messageBox.innerText = 'Login berhasil! Selamat datang, ' + user.name;

                    // Simpan token ke localStorage
                    localStorage.setItem('token', user.token);

                    // Redirect kalau perlu
                    // window.location.href = '/dashboard';
                } else {
                    messageBox.classList.add('error');
                    messageBox.innerText = 'Login gagal: ' + response.data.message;
                }
            })
            .catch(function (error) {
                messageBox.classList.add('error');
                if (error.response && error.response.data && error.response.data.message) {
                    messageBox.innerText = error.response.data.message;
                } else {
                    messageBox.innerText = 'Terjadi kesalahan. Silakan coba lagi.';
                }
            });
        });
    </script>

</body>
</html>
