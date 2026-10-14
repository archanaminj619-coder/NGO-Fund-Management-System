<?php
include "db.php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Public registrations receive the Staff role.
    $role = "Staff";

    if ($name === "" || $email === "" || $password === "") {
        $message = "Please complete all fields.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } else {

        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "This email is already registered.";
        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO users (name, email, password, role)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $name,
                $email,
                $hashed_password,
                $role
            );

            try {
    if (mysqli_stmt_execute($stmt)) {
        $message = "Registration successful! You can now sign in.";
        $success = true;
    } else {
        $message = "Registration could not be completed. Please try again.";
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        $message = "This email is already registered. Please sign in or use another email.";
    } else {
        error_log("Registration database error: " . $e->getMessage());
        $message = "Registration could not be completed. Please try again.";
    }
}

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | NGO Fund Management</title>

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
            min-height: 650px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(20, 60, 35, 0.15);
        }

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

        .form-panel {
            padding: 42px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-heading {
            margin-bottom: 25px;
        }

        .form-heading h2 {
            color: #173e2c;
            font-size: 29px;
            margin-bottom: 10px;
        }

        .form-heading p {
            color: #7b887e;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 17px;
        }

        label {
            display: block;
            color: #304538;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
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

        .hint {
            margin-top: 7px;
            color: #78857b;
            font-size: 12px;
        }

        .register-button {
            width: 100%;
            padding: 15px;
            margin-top: 5px;
            border: none;
            border-radius: 10px;
            background: #246443;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .register-button:hover {
            background: #174a30;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            color: #c62828;
            font-size: 13px;
            line-height: 1.6;
        }

        .message.success {
            color: #237343;
        }

        .login-link {
            margin-top: 22px;
            text-align: center;
            color: #78857b;
            font-size: 13px;
            line-height: 1.8;
        }

        .login-link a {
            color: #246443;
            font-weight: bold;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 22px;
            padding-top: 17px;
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

            .welcome-note {
                margin-top: 20px;
            }

            .form-panel {
                padding: 35px 28px;
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

            <h1>Be Part of<br>Something Good.</h1>

            <p>
                Create your account to help manage NGO activities,
                track resources, and support meaningful projects.
            </p>

            <div class="welcome-note">
                Small contributions can create big changes.<br>
                Let's build a better tomorrow together. 💚
            </div>

        </div>

    </section>

    <section class="form-panel">

        <div class="form-heading">
            <h2>Create Account</h2>
            <p>Fill in your details to get started.</p>
        </div>

        <form method="POST" action="register.php">

            <div class="form-group">
                <label for="name">Full Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    autocomplete="name"
                    maxlength="100"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    autocomplete="email"
                    maxlength="255"
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
                        placeholder="Create a password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword()"
                        aria-label="Show or hide password"
                    >Show</button>
                </div>

                <p class="hint">Use at least 8 characters.</p>
            </div>

            <button type="submit" class="register-button">
                Create Account →
            </button>

        </form>

        <div class="message <?php echo $success ? 'success' : ''; ?>">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <div class="login-link">
            Already have an account?<br>
            <a href="login.php">Sign In</a>
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
