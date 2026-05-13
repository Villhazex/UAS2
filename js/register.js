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