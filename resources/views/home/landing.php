<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supermarket Management System</title>
    <style>
        :root {
            --primary: #1E4AFF;
            --text: #ffffff;
            --overlay: rgba(8, 18, 48, 0.56);
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            height: 100%;
            overflow: hidden;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
        }
        .screen {
            position: relative;
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
            isolation: isolate;
        }
        .screen::before {
            content: "";
            position: absolute;
            inset: -20px;
            background-image: url("https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=2000&q=80");
            background-size: cover;
            background-position: center;
            filter: blur(5px);
            transform: scale(1.05);
            z-index: -2;
        }
        .screen::after {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--overlay);
            z-index: -1;
        }
        .wrap {
            width: min(920px, 100%);
        }
        .logo {
            width: 104px;
            height: 104px;
            margin: 0 auto 20px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            display: grid;
            place-items: center;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 0.08em;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }
        h1 {
            margin: 0;
            font-size: clamp(28px, 4vw, 56px);
            line-height: 1.15;
            letter-spacing: 0.01em;
        }
        .subtitle {
            margin: 14px 0 34px;
            font-size: clamp(14px, 2vw, 22px);
            opacity: 0.94;
            letter-spacing: 0.05em;
        }
        .actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 14px;
            width: min(760px, 100%);
            margin: 0 auto;
        }
        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            height: 58px;
            border-radius: 999px;
            text-decoration: none;
            color: #fff;
            background: var(--primary);
            font-weight: 700;
            font-size: 16px;
            border: 0;
            box-shadow: 0 16px 32px rgba(30, 74, 255, 0.35);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 34px rgba(30, 74, 255, 0.45);
        }
        @media (max-width: 768px) {
            .actions {
                grid-template-columns: 1fr;
            }
            .btn {
                height: 54px;
            }
        }
    </style>
</head>
<body>
<section class="screen">
    <div class="wrap">
        <div class="logo">SMS</div>
        <h1>Supermarket Management System</h1>
        <p class="subtitle">Fast Billing • Smart Stock • Multi-Shop Control</p>
        <div class="actions">
            <a class="btn" href="<?php echo e(url('/staff-login')); ?>">Staff Login</a>
            <a class="btn" href="<?php echo e(url('/branch-login')); ?>">Branch Login</a>
            <a class="btn" href="<?php echo e(url('/admin-login')); ?>">Admin Login</a>
        </div>
    </div>
</section>
</body>
</html>
