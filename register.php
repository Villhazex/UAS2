<?php
session_start();

$error   = $_SESSION['register_error']   ?? null;
$success = $_SESSION['register_success'] ?? null;

unset($_SESSION['register_error'], $_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>Daftar — Luminous Zine</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Anybody:wght@300;400;600;700;800;900&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/register.css">
</head>

<body>

<!-- ═══ LEFT PANEL ═══ -->
<div class="panel-left">

    <div class="issue-tag">
        ✦ Luminous &nbsp;·&nbsp; Vol. <?= date('Y') ?>
    </div>

    <div class="hero-text">

        <div class="hero-eyebrow">
            Mulai perjalananmu
        </div>

        <div class="hero-title">

            <div class="ghost" aria-hidden="true">
                Buat<br><em>akun</em>
            </div>

            <div>
                Buat<br><em>akun</em>
            </div>

        </div>

        <p class="hero-desc">
            Bergabung dan mulai kelola tugasmu dengan tampilan yang estetik dan sistem yang terstruktur.
        </p>

        <div class="steps">

            <div class="step">
                <div class="step-num">01</div>

                <div class="step-content">
                    <div class="step-title">Isi data diri</div>

                    <div class="step-desc">
                        Username, email, dan password yang kuat
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-num">02</div>

                <div class="step-content">
                    <div class="step-title">Masuk ke dashboard</div>

                    <div class="step-desc">
                        Langsung akses semua fitur manajemen tugas
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-num">03</div>

                <div class="step-content">
                    <div class="step-title">Mulai produktif</div>

                    <div class="step-desc">
                        Tambah, kelola, dan selesaikan tugasmu
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel-left-footer">
        Tetap Semangat, pantang menyerah ✦
    </div>
</div>

<!-- ═══ RIGHT PANEL ═══ -->
<div class="panel-right">

    <div class="corner-tl"></div>
    <div class="corner-br"></div>

    <div class="form-wrap">

        <div class="form-tag">✦ Sign Up</div>

        <div class="form-headline">
            Daftar akun<br><em>baru</em>
        </div>

        <p class="form-sub">
            <?= date('d M Y') ?>
            &nbsp;·&nbsp;
            Gratis & Mudah
        </p>

        <?php if($error): ?>
            <div class="alert-error">
                ⚠ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert-success">
                ✓ <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="proses_register.php" method="POST" id="regForm" novalidate>

            <div class="form-grid">

                <!-- Nama Depan -->
                <div class="form-group">

                    <label class="form-label" for="nama_depan">
                        Nama Depan
                    </label>

                    <input
                        class="form-input"
                        type="text"
                        id="nama_depan"
                        name="nama_depan"
                        placeholder="nama depan..."
                        required
                        autocomplete="given-name"
                    >
                </div>

                <!-- Nama Belakang -->
                <div class="form-group">

                    <label class="form-label" for="nama_belakang">
                        Nama Belakang
                    </label>

                    <input
                        class="form-input"
                        type="text"
                        id="nama_belakang"
                        name="nama_belakang"
                        placeholder="nama belakang..."
                        autocomplete="family-name"
                    >
                </div>

                <!-- Username -->
                <div class="form-group full">

                    <label class="form-label" for="username">
                        Username
                    </label>

                    <input
                        class="form-input"
                        type="text"
                        id="username"
                        name="username"
                        placeholder="pilih username unikmu..."
                        required
                        autocomplete="username"
                        minlength="3"
                    >

                    <span class="form-hint">
                        Minimal 3 karakter, tanpa spasi
                    </span>
                </div>

                <!-- Email -->
                <div class="form-group full">

                    <label class="form-label" for="email">
                        Email
                    </label>

                    <input
                        class="form-input"
                        type="email"
                        id="email"
                        name="email"
                        placeholder="email@contoh.com"
                        required
                        autocomplete="email"
                    >
                </div>

                <!-- Password -->
                <div class="form-group full">

                    <label class="form-label" for="password">
                        Password
                    </label>

                    <div class="pw-wrap">

                        <input
                            class="form-input"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="buat password yang kuat..."
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >

                        <button
                            type="button"
                            class="pw-toggle"
                            id="pwToggle1"
                            title="Tampilkan password"
                        >
                            👁
                        </button>

                    </div>

                    <div class="strength-wrap">

                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>

                        <div class="strength-label" id="strengthLabel">
                            Ketik password...
                        </div>

                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group full">

                    <label class="form-label" for="confirm_password">
                        Konfirmasi Password
                    </label>

                    <div class="pw-wrap">

                        <input
                            class="form-input"
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="ulangi password..."
                            required
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            class="pw-toggle"
                            id="pwToggle2"
                            title="Tampilkan password"
                        >
                            👁
                        </button>

                    </div>

                    <span class="form-hint" id="matchHint"></span>
                </div>

            </div>

            <!-- Terms -->
            <div class="terms-row">

                <input
                    type="checkbox"
                    class="terms-checkbox"
                    id="terms"
                    name="terms"
                    required
                >

                <label class="terms-text" for="terms">
                    Dengan mendaftar, kamu menyetujui
                    <a href="#">syarat &amp; ketentuan</a>
                    serta
                    <a href="#">kebijakan privasi</a>
                    Luminous.
                </label>

            </div>

            <button class="form-submit" type="submit" id="submitBtn" disabled>
                ✦ Buat Akun Sekarang
            </button>

        </form>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">sudah punya akun?</span>
            <div class="divider-line"></div>
        </div>

        <div class="login-row">
            <a href="login.php" class="login-btn">
                → Masuk Sekarang
            </a>
        </div>

    </div>
</div>

<script>
    /* ── PASSWORD TOGGLE ── */

function makePwToggle(toggleId, inputId) {

    const btn   = document.getElementById(toggleId);
    const input = document.getElementById(inputId);

    btn.addEventListener('click', () => {

        const show = input.type === 'password';

        input.type = show ? 'text' : 'password';

        btn.textContent = show ? '🙈' : '👁';
    });
}

makePwToggle('pwToggle1', 'password');
makePwToggle('pwToggle2', 'confirm_password');

/* ── PASSWORD STRENGTH ── */

const pwInput      = document.getElementById('password');
const strengthFill = document.getElementById('strengthFill');
const strengthLbl  = document.getElementById('strengthLabel');

function calcStrength(pw) {

    let score = 0;

    if (pw.length >= 6) score++;
    if (pw.length >= 10) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    return score;
}

const levels = [

    {
        pct: 0,
        color: 'transparent',
        label: 'Ketik password...'
    },

    {
        pct: 20,
        color: '#c10000',
        label: 'Sangat lemah'
    },

    {
        pct: 40,
        color: '#ff5c8a',
        label: 'Lemah'
    },

    {
        pct: 60,
        color: '#b87200',
        label: 'Cukup'
    },

    {
        pct: 80,
        color: '#3366ff',
        label: 'Kuat'
    },

    {
        pct: 100,
        color: '#00cc77',
        label: 'Sangat kuat ✓'
    }
];

pwInput.addEventListener('input', () => {

    const score = pwInput.value
        ? Math.max(1, calcStrength(pwInput.value))
        : 0;

    const lv = levels[score];

    strengthFill.style.width = lv.pct + '%';
    strengthFill.style.background = lv.color;

    strengthLbl.textContent = lv.label;

    strengthLbl.style.color =
        lv.color === 'transparent'
        ? 'rgba(26,18,8,0.3)'
        : lv.color;

    validate();
});

/* ── CONFIRM PASSWORD ── */

const confirmInput = document.getElementById('confirm_password');
const matchHint    = document.getElementById('matchHint');

confirmInput.addEventListener('input', () => {

    if (!confirmInput.value) {

        matchHint.textContent = '';
        return;
    }

    const match = confirmInput.value === pwInput.value;

    matchHint.textContent =
        match
        ? '✓ Password cocok'
        : '✕ Password tidak cocok';

    matchHint.style.color =
        match
        ? '#006b38'
        : '#c10000';

    confirmInput.classList.toggle('valid', match);
    confirmInput.classList.toggle('invalid', !match);

    validate();
});

/* ── USERNAME VALIDATION ── */

const usernameInput = document.getElementById('username');

usernameInput.addEventListener('input', () => {

    const val = usernameInput.value;

    const ok = val.length >= 3 && !/\s/.test(val);

    usernameInput.classList.toggle(
        'valid',
        ok && val.length > 0
    );

    usernameInput.classList.toggle(
        'invalid',
        !ok && val.length > 0
    );

    validate();
});

/* ── EMAIL VALIDATION ── */

const emailInput = document.getElementById('email');

emailInput.addEventListener('input', () => {

    const ok =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    .test(emailInput.value);

    emailInput.classList.toggle('valid', ok);

    emailInput.classList.toggle(
        'invalid',
        !ok && emailInput.value.length > 0
    );

    validate();
});

/* ── ENABLE SUBMIT ── */

const termsCheck = document.getElementById('terms');
const submitBtn  = document.getElementById('submitBtn');

termsCheck.addEventListener('change', validate);

function validate() {

    const pwOk =
        calcStrength(pwInput.value) >= 2;

    const matchOk =
        confirmInput.value === pwInput.value
        &&
        confirmInput.value.length > 0;

    const unameOk =
        usernameInput.value.length >= 3
        &&
        !/\s/.test(usernameInput.value);

    const emailOk =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        .test(emailInput.value);

    const namaOk =
        document.getElementById('nama_depan')
        .value.trim().length > 0;

    submitBtn.disabled = !(
        pwOk &&
        matchOk &&
        unameOk &&
        emailOk &&
        namaOk &&
        termsCheck.checked
    );
}

document
.getElementById('nama_depan')
.addEventListener('input', validate);
</script>

</body>
</html>