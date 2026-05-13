    /* ─── TOAST ─── */
function showToast(msg, type = 'ok') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');

    t.className = 'toast' + (type === 'error' ? ' error' : '');
    t.textContent = msg;

    c.appendChild(t);

    setTimeout(() => t.remove(), 3300);
}

/* ─── FILTER + SEARCH + SORT + PRIORITY FILTER ─── */

const searchInput  = document.getElementById('searchInput');
const filterTabs   = document.querySelectorAll('.filter-tab');
const priorityBtns = document.querySelectorAll('.priority-dot-btn');
const cardGrid     = document.getElementById('cardGrid');
const noResults    = document.getElementById('noResults');

stickyFill.style.width = pct + '%';

const progCircle = document.querySelector('.prog-fill');

const offset = 188 - (1.88 * pct);

progCircle.style.strokeDashoffset = offset;

let currentFilter = 'all';
let currentPrio   = 'all';

function applyFilters() {

    const query = searchInput.value.toLowerCase().trim();

    const cards = [...cardGrid.querySelectorAll('.task-card')];

    let visible = 0;

    cards.forEach(card => {

        const matchFilter =
            currentFilter === 'all' ||
            (currentFilter === 'done' &&
                card.dataset.status === 'done') ||
            (currentFilter === 'pending' &&
                card.dataset.status === 'pending');

        const matchSearch =
            card.dataset.name.includes(query);

        const matchPrio =
            currentPrio === 'all' ||
            card.dataset.prio === currentPrio;

        if (matchFilter && matchSearch && matchPrio) {

            card.classList.remove('hidden-card');
            visible++;

        } else {

            card.classList.add('hidden-card');
        }
    });

    noResults.classList.toggle(
        'show',
        visible === 0 && cards.length > 0
    );
}

/* FILTER TAB */

filterTabs.forEach(tab => {

    tab.addEventListener('click', () => {

        filterTabs.forEach(t =>
            t.classList.remove('active')
        );

        tab.classList.add('active');

        currentFilter = tab.dataset.filter;

        applyFilters();
    });
});

/* PRIORITY FILTER */

priorityBtns.forEach(btn => {

    btn.addEventListener('click', () => {

        priorityBtns.forEach(b =>
            b.classList.remove('active')
        );

        btn.classList.add('active');

        currentPrio = btn.dataset.prio;

        applyFilters();
    });
});

searchInput.addEventListener('input', applyFilters);

/* ─── INLINE EDIT ─── */

function startEdit(btn) {

    const card    = btn.closest('.task-card');
    const title   = card.querySelector('.card-title');
    const input   = card.querySelector('.card-edit-input');
    const saveBtn = card.querySelector('.card-btn.save');

    title.style.display = 'none';
    input.style.display = 'block';

    input.focus();
    input.select();

    saveBtn.style.display = 'inline-flex';

    btn.style.display = 'none';
}

function saveEdit(btn, id) {

    const card    = btn.closest('.task-card');

    const title   = card.querySelector('.card-title');

    const input   = card.querySelector('.card-edit-input');

    const newVal  = input.value.trim();

    if (!newVal) {

        showToast(
            'Nama tugas tidak boleh kosong!',
            'error'
        );

        return;
    }

    fetch(`edit.php?id=${id}&nama=${encodeURIComponent(newVal)}`)

        .then(r => r.json())

        .then(d => {

            if (d.ok) {

                title.textContent = newVal;

                card.dataset.name = newVal.toLowerCase();

                input.value = newVal;

                showToast('✓ Tugas berhasil diubah');

            } else {

                showToast('Gagal menyimpan', 'error');
            }
        })

        .catch(() =>
            showToast('Gagal menyimpan', 'error')
        );

    title.style.display = '';

    input.style.display = 'none';

    btn.style.display = 'none';

    editBtn.style.display = 'inline-flex';
}

/* ─── CONFETTI ─── */

if (pct === 100 && totalTask > 0) {

    setTimeout(() => launchConfetti(), 400);
}

function launchConfetti() {

    const canvas = document.getElementById('confetti-canvas');

    canvas.classList.add('active');

    const ctx = canvas.getContext('2d');

    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;

    const colors = [
        '#ff5c8a',
        '#3366ff',
        '#00cc77',
        '#ffd166',
        '#c1006b',
        '#6699ff'
    ];

    const bits = Array.from({ length: 120 }, () => ({

        x: Math.random() * canvas.width,

        y: -20 - Math.random() * 200,

        w: 6 + Math.random() * 10,

        h: 8 + Math.random() * 6,

        r: Math.random() * Math.PI * 2,

        dr: (Math.random() - 0.5) * 0.15,

        vx: (Math.random() - 0.5) * 3,

        vy: 2 + Math.random() * 3,

        c: colors[
            Math.floor(Math.random() * colors.length)
        ]
    }));

    let frame = 0;

    function draw() {

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        bits.forEach(b => {

            ctx.save();

            ctx.translate(b.x, b.y);

            ctx.rotate(b.r);

            ctx.fillStyle = b.c;

            ctx.fillRect(
                -b.w / 2,
                -b.h / 2,
                b.w,
                b.h
            );

            ctx.restore();

            b.x += b.vx;
            b.y += b.vy;
            b.r += b.dr;
        });

        frame++;

        if (frame < 140) {

            requestAnimationFrame(draw);

        } else {

            canvas.classList.remove('active');

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );
        }
    }

    draw();

    showToast(
        '🎉 Semua tugas selesai! Luar biasa!'
    );
}