<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"];

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign In | NGO Fund Management</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f3f7f3;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            min-height: 580px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(20, 60, 35, 0.15);
        }

        /* Left welcome section */

        .welcome-panel {
            background: linear-gradient(145deg, #123c2a, #286847);
            color: white;
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-panel::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            right: -120px;
            top: -100px;
        }

        .welcome-panel::after {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 50%;
            left: -100px;
            bottom: -100px;
        }

        .brand {
            position: absolute;
            top: 30px;
            left: 35px;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-icon {
            width: 82px;
            height: 82px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            font-size: 42px;
            margin-bottom: 30px;
        }

        .welcome-content h1 {
            font-size: 35px;
            line-height: 1.25;
            margin-bottom: 20px;
        }

        .welcome-content p {
            color: #dcebe0;
            font-size: 15px;
            line-height: 1.9;
            max-width: 360px;
        }

        .welcome-note {
            margin-top: 35px;
            padding: 16px;
            border-left: 3px solid #a6d8b4;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 0 9px 9px 0;
            color: #edf7ef;
            font-size: 13px;
            line-height: 1.7;
        }

        /* Right login section */

        .form-panel {
            padding: 55px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-heading {
            margin-bottom: 32px;
        }

        .form-heading h2 {
            color: #173e2c;
            font-size: 30px;
            margin-bottom: 10px;
        }

        .form-heading p {
            color: #7b887e;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            color: #304538;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 9px;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 1px solid #dce6de;
            border-radius: 10px;
            background: #fbfdfb;
            color: #24392c;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #388457;
            box-shadow: 0 0 0 3px rgba(56, 132, 87, 0.12);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 70px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #267044;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            padding: 5px;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #246443;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-button:hover {
            background: #174a30;
        }

        .message {
            margin-top: 16px;
            text-align: center;
            color: #c62828;
            font-size: 13px;
            line-height: 1.6;
        }

        .register-link {
            margin-top: 28px;
            text-align: center;
            color: #78857b;
            font-size: 13px;
            line-height: 1.8;
        }

        .register-link a {
            color: #246443;
            font-weight: bold;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #edf1ed;
            text-align: center;
            color: #89938b;
            font-size: 11px;
        }

        @media (max-width: 750px) {
            body {
                padding: 15px;
            }

            .container {
                max-width: 480px;
                grid-template-columns: 1fr;
            }

            .welcome-panel {
                padding: 75px 30px 35px;
                min-height: auto;
            }

            .brand {
                top: 25px;
                left: 30px;
            }

            .welcome-icon {
                width: 60px;
                height: 60px;
                font-size: 30px;
                margin-bottom: 18px;
            }

            .welcome-content h1 {
                font-size: 27px;
            }

            .welcome-content p {
                font-size: 14px;
            }

            .welcome-note {
                margin-top: 20px;
            }

            .form-panel {
                padding: 35px 28px;
            }

            .form-heading h2 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <section class="welcome-panel">

        <div class="brand">🌿 NGO MANAGER</div>

        <div class="welcome-content">

            <div class="welcome-icon">🌱</div>

            <h1>Together,<br>We Make a Difference.</h1>

            <p>
                Welcome to your NGO Fund Management System.
                Manage donations, track expenses, and organize
                projects in one place.
            </p>

            <div class="welcome-note">
                Every contribution counts.<br>
                Every act of kindness matters. 💚
            </div>

        </div>

    </section>

    <section class="form-panel">

        <div class="form-heading">
            <h2>Sign In</h2>
            <p>Welcome back! Please enter your account details.</p>
        </div>

        <form method="POST" action="login.php">

            <div class="form-group">
                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword()"
                        aria-label="Show or hide password"
                    >Show</button>
                </div>
            </div>

            <button type="submit" class="login-button">
                Sign In →
            </button>

        </form>

        <div class="message">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <div class="register-link">
            Don't have an account?<br>
            <a href="register.php">Create an account</a>
        </div>

        <div class="footer">
            NGO Fund Management System · Making a difference together
        </div>

    </section>

</div>

<script>
    function togglePassword() {
        const password = document.getElementById("password");
        const button = document.querySelector(".toggle-password");

        if (password.type === "password") {
            password.type = "text";
            button.textContent = "Hide";
        } else {
            password.type = "password";
            button.textContent = "Show";
        }
    }
</script>

</body>
</html>
