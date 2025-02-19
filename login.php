<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* memastikan body menutupi seluruh viewport */
        }

        .container {
            width: 350px; /* sedikit lebih lebar */
            padding: 20px;
            background-color: white;
            border-radius: 8px; /* border radius lebih besar */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333; /* warna teks lebih gelap */
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555; /* warna teks label lebih gelap */
        }

        .input-group { /* untuk mengelompokkan label dan input */
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 40px); /* menyesuaikan lebar input */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            background-color: #f8f8f8; /* warna input lebih terang */
            padding-left: 40px; /* memberikan ruang untuk ikon */
            position: relative; /* untuk memposisikan ikon */
        }
        
        .input-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff; /* warna biru */
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3; /* warna biru lebih gelap saat hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Login</h2>
        <form action="proses_login.php" method="POST">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <i class="fas fa-user"></i>  </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>

    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script> 
</body>
</html>