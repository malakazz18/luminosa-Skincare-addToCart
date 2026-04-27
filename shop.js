const cartSidebar = document.getElementById('cartSidebar');
const cartOverlay = document.getElementById('cartOverlay');
const cartToggle  = document.getElementById('cartToggle');
const cartCloseBtn = document.getElementById('cartClose');
const cartItemsEl = document.getElementById('cartItems');
const cartFooter  = document.getElementById('cartFooter');
const cartBadge   = document.getElementById('cartBadge');
const cartTotalEl = document.getElementById('cartTotal');
const btnClear    = document.getElementById('btnClear');
const toast       = document.getElementById('toast');


const openCart  = () => { cartSidebar.classList.add('open');    cartOverlay.classList.add('active'); }
const closeCart = () => { cartSidebar.classList.remove('open'); cartOverlay.classList.remove('active'); }

cartToggle.addEventListener('click', openCart);
cartCloseBtn.addEventListener('click', closeCart);
cartOverlay.addEventListener('click', closeCart);
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeCart(); closeAuth(); } });


let toastTimer;
function showToast(msg, duration = 2500) {
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), duration);
}


async function cartAction(data) {
    if (typeof CSRF_TOKEN !== 'undefined') data.csrf_token = CSRF_TOKEN;
    const body = new URLSearchParams(data);
    try {
        const res  = await fetch('action_panier.php', { method: 'POST', body });
        const json = await res.json();
        if (json.success) renderCart(json);
        else console.error('Cart error:', json.error);
    } catch (err) {
        console.error('Fetch failed:', err);
    }
}

function renderCart({ items, total, count }) {

    if (count > 0) {
        cartBadge.textContent = count;
        cartBadge.classList.add('visible');
    } else {
        cartBadge.textContent = '';
        cartBadge.classList.remove('visible');
    }


    if (cartTotalEl) cartTotalEl.textContent = formatPrice(total);

    if (cartFooter) cartFooter.style.display = items.length ? '' : 'none';


    if (!cartItemsEl) return;

    if (items.length === 0) {
        cartItemsEl.innerHTML = '<p class="cart-empty">Votre panier est vide.</p>';
        return;
    }

    cartItemsEl.innerHTML = items.map(item => `
        <div class="cart-item" data-id="${item.id}">
            <img src="${escHtml(item.image)}" alt="${escHtml(item.name)}">
            <div class="cart-item-info">
                <p class="cart-item-name">${escHtml(item.name)}</p>
                <p class="cart-item-price">${formatPrice(item.price)}</p>
                <div class="qty-control">
                    <button class="qty-btn" data-action="decrement">−</button>
                    <span class="qty-value">${item.quantity}</span>
                    <button class="qty-btn" data-action="increment">+</button>
                </div>
            </div>
            <div class="cart-item-right">
                <span class="cart-item-subtotal">${formatPrice(item.subtotal)}</span>
                <button class="cart-item-remove" aria-label="Retirer l'article">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    `).join('');

    attachCartItemEvents();
}

function attachCartItemEvents() {
    if (!cartItemsEl) return;
    cartItemsEl.querySelectorAll('.cart-item').forEach(row => {
        const id = parseInt(row.dataset.id, 10);
        row.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const qtyEl = row.querySelector('.qty-value');
                let qty = parseInt(qtyEl.textContent, 10);
                if (btn.dataset.action === 'increment') qty++;
                else qty = Math.max(0, qty - 1);
                cartAction({ action: 'update', product_id: id, quantity: qty });
            });
        });
        row.querySelector('.cart-item-remove').addEventListener('click', () => {
            cartAction({ action: 'remove', product_id: id });
            showToast('Article retiré du panier');
        });
    });
}

document.querySelectorAll('.btn-add').forEach(btn => {
    btn.addEventListener('click', () => {
        const id   = btn.dataset.id;
        const name = btn.dataset.name;
        cartAction({ action: 'add', product_id: id, quantity: 1 });
        showToast(`"${name}" ajouté au panier`);
        openCart();
    });
});


if (btnClear) {
    btnClear.addEventListener('click', () => {
        if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
            cartAction({ action: 'clear' });
            showToast('Panier vidé');
        }
    });
}


function formatPrice(n) {
    return parseFloat(n).toFixed(2) + ' TND';
}
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}


attachCartItemEvents();



const authOverlayEl  = document.getElementById('authOverlay');
const authModalEl    = document.getElementById('authModal');
const authCloseBtnEl = document.getElementById('authClose');
const btnOpenAuthEl  = document.getElementById('btnOpenAuth');
const btnCheckoutLoginEl = document.getElementById('btnCheckoutLogin');

function openAuth(tab = 'login') {
    authOverlayEl.classList.add('active');
    switchTab(tab);
}
function closeAuth() {
    if (authOverlayEl) authOverlayEl.classList.remove('active');
}


if (btnOpenAuthEl) btnOpenAuthEl.addEventListener('click', () => openAuth('login'));


if (btnCheckoutLoginEl) btnCheckoutLoginEl.addEventListener('click', () => {
    closeCart();
    openAuth('login');
});

if (authCloseBtnEl) authCloseBtnEl.addEventListener('click', e => {
    e.stopPropagation();
    closeAuth();
});

if (authOverlayEl) authOverlayEl.addEventListener('click', e => {
    if (e.target === authOverlayEl) closeAuth();
});


document.querySelectorAll('.auth-tab').forEach(tab => {
    tab.addEventListener('click', () => switchTab(tab.dataset.tab));
});
document.querySelectorAll('.auth-switch a').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        switchTab(a.dataset.switch);
    });
});

function switchTab(name) {
    document.querySelectorAll('.auth-tab').forEach(t =>
        t.classList.toggle('active', t.dataset.tab === name)
    );
    document.querySelectorAll('.auth-panel').forEach(p =>
        p.classList.toggle('active', p.id === 'tab' + name.charAt(0).toUpperCase() + name.slice(1))
    );
}


function showAuthErrors(containerId, errors) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = errors.map(e => `<p>${e}</p>`).join('');
    el.style.display = errors.length ? 'block' : 'none';
}
function clearAuthErrors(containerId) {
    const el = document.getElementById(containerId);
    if (el) { el.innerHTML = ''; el.style.display = 'none'; }
}

const btnLoginEl = document.getElementById('btnLogin');
if (btnLoginEl) {
    btnLoginEl.addEventListener('click', async () => {
        clearAuthErrors('loginErrors');
        btnLoginEl.disabled = true;
        btnLoginEl.textContent = 'Connexion…';

        const body = new URLSearchParams({
            action:      'login',
            email:       document.getElementById('loginEmail').value.trim(),
            password:    document.getElementById('loginPassword').value,
            csrf_token:  (typeof CSRF_TOKEN !== 'undefined') ? CSRF_TOKEN : '',
        });

        try {
            const res  = await fetch('auth.php', { method: 'POST', body });
            const json = await res.json();
            if (json.success) {
                closeAuth();
                showToast('Bienvenue ! 🌸');
                setTimeout(() => location.reload(), 800);
            } else {
                showAuthErrors('loginErrors', json.errors || ['Erreur inconnue.']);
            }
        } catch (e) {
            showAuthErrors('loginErrors', ['Erreur réseau, veuillez réessayer.']);
            console.error(e);
        }

        btnLoginEl.disabled = false;
        btnLoginEl.innerHTML = '<i class="ri-login-box-line"></i> Se connecter';
    });
}

const btnRegisterEl = document.getElementById('btnRegister');
if (btnRegisterEl) {
    btnRegisterEl.addEventListener('click', async () => {
        clearAuthErrors('registerErrors');
        btnRegisterEl.disabled = true;
        btnRegisterEl.textContent = 'Création…';

        const body = new URLSearchParams({
            action:           'register',
            first_name:       document.getElementById('regFirstName').value.trim(),
            last_name:        document.getElementById('regLastName').value.trim(),
            email:            document.getElementById('regEmail').value.trim(),
            phone:            document.getElementById('regPhone').value.trim(),
            address:          document.getElementById('regAddress').value.trim(),
            password:         document.getElementById('regPassword').value,
            password_confirm: document.getElementById('regConfirm').value,
            csrf_token:       (typeof CSRF_TOKEN !== 'undefined') ? CSRF_TOKEN : '',
        });

        try {
            const res  = await fetch('auth.php', { method: 'POST', body });
            const json = await res.json();
            if (json.success) {
                closeAuth();
                showToast('Compte créé ! Bienvenue 🌸');
                setTimeout(() => location.reload(), 800);
            } else {
                showAuthErrors('registerErrors', json.errors || ['Erreur inconnue.']);
            }
        } catch (e) {
            showAuthErrors('registerErrors', ['Erreur réseau, veuillez réessayer.']);
            console.error(e);
        }

        btnRegisterEl.disabled = false;
        btnRegisterEl.innerHTML = '<i class="ri-user-add-line"></i> Créer mon compte';
    });
}

const userMenuBtnEl  = document.getElementById('userMenuBtn');
const userDropdownEl = document.getElementById('userDropdown');
if (userMenuBtnEl && userDropdownEl) {
    userMenuBtnEl.addEventListener('click', e => {
        e.stopPropagation();
        userDropdownEl.classList.toggle('open');
    });
    document.addEventListener('click', () => userDropdownEl.classList.remove('open'));
}

const btnLogoutEl = document.getElementById('btnLogout');
if (btnLogoutEl) {
    btnLogoutEl.addEventListener('click', async () => {
        await fetch('auth.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'logout' })
        });
        location.reload();
    });
}