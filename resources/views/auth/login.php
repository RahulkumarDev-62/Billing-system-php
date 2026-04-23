<?php $email = $email ?? ''; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e((string) $title); ?></title>
    <style>
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #eef3ff; margin: 0; }
        .wrap { max-width: 460px; margin: 50px auto; padding: 28px; background: #fff; border: 1px solid #d6e1ff; border-radius: 20px; box-shadow: 0 20px 40px rgba(16, 24, 40, 0.08); }
        .field { margin-bottom: 14px; }
        input { width: 100%; padding: 11px 12px; border-radius: 12px; border: 1px solid #d6e1ff; }
        button, a { display: inline-block; padding: 10px 14px; border-radius: 12px; text-decoration: none; }
        button { border: 0; background: #1E4AFF; color: #fff; font-weight: 700; }
        .error { color: #b03a2e; font-size: 13px; margin-top: 6px; }
        .muted { color: #59667a; font-size: 14px; }
        .row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 6px; }
        .ghost { background: #fff; color: #1E4AFF; border: 1px solid #1E4AFF; }
        .tabs { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 16px; }
        .tab { text-align: center; padding: 9px 10px; border-radius: 10px; border: 1px solid #c9d8ff; color: #1E4AFF; cursor: pointer; font-size: 13px; }
        .tab.active { background: #1E4AFF; color: #fff; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1><?php echo e(ucfirst((string) $role)); ?> Login</h1>
        <p class="muted">Role based secure access panel</p>

        <?php if (!empty($allowOtp)): ?>
            <div class="tabs">
                <div class="tab active" data-mode="password">Email + Password</div>
                <div class="tab" data-mode="otp">Email OTP</div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors['login'])): ?><p class="error"><?php echo e((string) $errors['login']); ?></p><?php endif; ?>

        <form id="passwordForm" method="post" action="<?php echo e(url('/' . $role . '-login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="field">
                <input type="email" name="email" placeholder="Email" value="<?php echo e((string) $email); ?>">
                <?php if (!empty($errors['email'])): ?><div class="error"><?php echo e((string) $errors['email']); ?></div><?php endif; ?>
            </div>
            <div class="field">
                <input type="password" name="password" placeholder="Password">
                <?php if (!empty($errors['password'])): ?><div class="error"><?php echo e((string) $errors['password']); ?></div><?php endif; ?>
            </div>
            <button type="submit">Login</button>
        </form>

        <?php if (!empty($allowOtp)): ?>
            <form id="otpForm" class="hidden" method="post" action="<?php echo e(url('/' . $role . '-login/request-otp')); ?>">
                <?php echo csrf_field(); ?>
                <div class="field">
                    <input type="email" name="email" placeholder="Email" value="<?php echo e((string) $email); ?>">
                    <?php if (!empty($errors['otp'])): ?><div class="error"><?php echo e((string) $errors['otp']); ?></div><?php endif; ?>
                </div>
                <div class="row">
                    <button type="submit">Send OTP</button>
                </div>
            </form>

            <form id="verifyForm" class="<?php echo !empty($otpStep) ? '' : 'hidden'; ?>" method="post" action="<?php echo e(url('/' . $role . '-login/verify-otp')); ?>" style="margin-top:12px;">
                <?php echo csrf_field(); ?>
                <div class="field">
                    <input type="email" name="email" placeholder="Email" value="<?php echo e((string) $email); ?>">
                </div>
                <div class="field">
                    <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6">
                </div>
                <button class="ghost" type="submit">Verify OTP</button>
            </form>

            <script>
                const tabs = document.querySelectorAll('.tab');
                const passwordForm = document.getElementById('passwordForm');
                const otpForm = document.getElementById('otpForm');
                const verifyForm = document.getElementById('verifyForm');
                tabs.forEach((tab) => {
                    tab.addEventListener('click', () => {
                        tabs.forEach(t => t.classList.remove('active'));
                        tab.classList.add('active');
                        const otpMode = tab.getAttribute('data-mode') === 'otp';
                        passwordForm.classList.toggle('hidden', otpMode);
                        otpForm.classList.toggle('hidden', !otpMode);
                        if (!otpMode) {
                            verifyForm.classList.add('hidden');
                        }
                    });
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>