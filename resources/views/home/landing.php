<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supermarket Management System</title>
    <style>
        :root {
            --primary: #1e40af;
            --secondary: #0ea5e9;
            --text: #ffffff;
            --overlay: rgba(7, 10, 28, 0.56);
            --glass: rgba(255, 255, 255, 0.12);
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            height: 100%;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--text);
        }

        .screen {
            position: relative;
            min-height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            isolation: isolate;
        }

        .screen::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("<?php echo e(url('/assets/hero-pattern.svg')); ?>");
            background-size: cover;
            background-position: center;
            filter: blur(1px);
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
            width: min(980px, 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            backdrop-filter: blur(12px);
            background: linear-gradient(135deg, rgba(12, 27, 71, 0.48), rgba(11, 45, 94, 0.3));
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            overflow: hidden;
        }

        .content {
            padding: 44px;
            text-align: left;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(14, 165, 233, 0.18);
            border: 1px solid rgba(186, 230, 253, 0.4);
            font-size: 13px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .logo {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.16);
            padding: 6px;
        }

        h1 {
            margin: 0;
            font-size: clamp(30px, 4.1vw, 54px);
            line-height: 1.1;
            letter-spacing: 0.02em;
        }

        .subtitle {
            margin: 16px 0 28px;
            font-size: clamp(14px, 1.9vw, 21px);
            opacity: 0.93;
            max-width: 500px;
        }

        .feature {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 30px;
        }

        .chip {
            border: 1px solid rgba(255, 255, 255, 0.34);
            background: rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 14px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            height: 58px;
            border-radius: 14px;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            font-weight: 700;
            font-size: 15px;
            border: 0;
            box-shadow: 0 16px 32px rgba(14, 165, 233, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 34px rgba(14, 165, 233, 0.4);
        }

        .panel-art {
            padding: 44px;
            border-left: 1px solid rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .art-card {
            width: min(420px, 100%);
            aspect-ratio: 1 / 1;
            border-radius: 24px;
            background: radial-gradient(circle at top, rgba(125, 211, 252, 0.4), rgba(14, 116, 144, 0.18));
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .art-card::before,
        .art-card::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
        }

        .art-card::before {
            width: 180px;
            height: 180px;
            top: 28px;
            right: 26px;
        }

        .art-card::after {
            width: 110px;
            height: 110px;
            bottom: 30px;
            left: 30px;
        }

        .reveal {
            opacity: 0;
            transform: translateY(12px);
            animation: reveal 0.6s forwards;
        }

        .reveal.delay { animation-delay: 0.15s; }
        .reveal.delay2 { animation-delay: 0.3s; }

        @keyframes reveal {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .wrap {
                grid-template-columns: 1fr;
            }

            .content,
            .panel-art {
                padding: 26px;
            }

            .actions {
                grid-template-columns: 1fr;
            }

            .btn {
                height: 54px;
            }

            .panel-art {
                border-left: 0;
                border-top: 1px solid rgba(255, 255, 255, 0.16);
            }
        }
    </style>
</head>
<body>
<section class="screen">
    <div class="wrap">
        <div class="content">
            <div class="badge reveal">
                <img class="logo" src="<?php echo e(url('/assets/logo.svg')); ?>" alt="SMS logo">
                Retail Automation Platform
            </div>
            <h1 class="reveal delay">Supermarket Management System</h1>
            <p class="subtitle reveal delay">Modern PHP + MySQL billing software with branch-ready operations, barcode flow, and sales intelligence.</p>
            <div class="feature reveal delay2">
                <span class="chip">Fast POS Billing</span>
                <span class="chip">Inventory Alerts</span>
                <span class="chip">Multi-Branch Reports</span>
                <span class="chip">OTP Role Login</span>
            </div>
            <div class="actions reveal delay2">
                <a class="btn" href="<?php echo e(url('/staff-login')); ?>">Staff Login</a>
                <a class="btn" href="<?php echo e(url('/branch-login')); ?>">Branch Login</a>
                <a class="btn" href="<?php echo e(url('/admin-login')); ?>">Admin Login</a>
            </div>
        </div>
        <div class="panel-art">
            <div class="art-card" aria-hidden="true"></div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(function (button) {
            button.addEventListener('mousemove', function (event) {
                const rect = button.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;
                button.style.background = 'radial-gradient(circle at ' + x + '% ' + y + '%, #38bdf8, #1e40af)';
            });
            button.addEventListener('mouseleave', function () {
                button.style.background = 'linear-gradient(135deg, #0ea5e9, #1e40af)';
            });
        });
    });
</script>
</body>
</html>
