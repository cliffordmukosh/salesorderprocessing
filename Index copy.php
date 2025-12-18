<?php
session_start();
require "db.php";

function mysql_password_hash($password) {
    return "*" . strtoupper(sha1(sha1($password, true)));
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT ID, Number, Name, Pass FROM cashier WHERE Number = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $cashier = $result->fetch_assoc();
        $computed = mysql_password_hash($password);

        if ($computed === $cashier['Pass']) {
            $_SESSION['cashier_id'] = $cashier['ID'];
            $_SESSION['cashier_number'] = $cashier['Number'];
            $_SESSION['cashier_name'] = $cashier['Name'];
            header("Location: sales.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vortex ERP - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --blue: #0078D7;
            --light-blue: #B3CCE6;
            --soft-blue: #9FBAD6;
            --bg: #F0F8FF;
            --card: #FFFFFF;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
        }

        .login-box {
            width: 380px;
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,120,215,0.2);
            overflow: hidden;
            border: 1px solid #BBDEFB;
        }

        .login-header {
            background: var(--blue);
            color: white;
            padding: 16px 20px;
            text-align: center;
            font-weight: 600;
            font-size: 1.35rem;
        }

        .login-header i {
            margin-right: 10px;
            font-size: 1.4rem;
        }

        .login-body {
            padding: 30px 35px 35px;
        }

        .form-control {
            height: 48px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 0 14px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0,120,215,0.15);
        }

        .input-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 4px;
        }

        .toggle-password:hover { color: var(--blue); }

        .btn-login {
            height: 50px;
            background: var(--blue);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 1.05rem;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #0066C0;
        }

        .alert {
            border-radius: 8px;
            font-size: 0.92rem;
            padding: 10px 14px;
        }

        .footer {
            text-align: center;
            padding: 12px;
            background: #f8f9fa;
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-header">
        <i class="fas fa-cash-register"></i>
        Vortex ERP Login
    </div>

    <div class="login-body">
        <?php if ($message): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label fw-semibold">Cashier Number</label>
                <input type="text" name="username" class="form-control" required autofocus placeholder="Enter number">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordField" class="form-control" required placeholder="Enter password">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">
                Login
            </button>
        </form>
    </div>

    <div class="footer">
        &copy; <?= date('Y') ?> Vortex ERP &bull; All rights reserved
    </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('passwordField');
    const icon = document.getElementById('eyeIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

</body>
</html>