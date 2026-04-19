
const cartSidebar = document.getElementById('cartSidebar');
const cartOverlay = document.getElementById('cartOverlay');
const cartToggle  = document.getElementById('cartToggle');
const cartClose   = document.getElementById('cartClose');
const cartItems   = document.getElementById('cartItems');
const cartFooter  = document.getElementById('cartFooter');
const cartBadge   = document.getElementById('cartBadge');
const cartTotal   = document.getElementById('cartTotal');
const btnClear    = document.getElementById('btnClear');
const toast       = document.getElementById('toast');


const openCart  = () => { cartSidebar.classList.add('open');  cartOverlay.classList.add('active'); }
const closeCart = () => { cartSidebar.classList.remove('open'); cartOverlay.classList.remove('active'); }

cartToggle.addEventListener('click', openCart);
cartClose .addEventListener('click', closeCart);
cartOverlay.addEventListener('click', closeCart);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCart(); });


let toastTimer;
function showToast(msg, duration = 2500) {
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), duration);
}

async function cartAction(data) {
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


    if (cartTotal) cartTotal.textContent = formatPrice(total);

    if (cartFooter) cartFooter.style.display = items.length ? '' : 'none';

 
    if (!cartItems) return;

    if (items.length === 0) {
        cartItems.innerHTML = '<p class="cart-empty">Your cart is empty.</p>';
        return;
    }

    cartItems.innerHTML = items.map(item => `
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
                <button class="cart-item-remove" aria-label="Remove item">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    `).join('');

    attachCartItemEvents();
}

function attachCartItemEvents() {

    cartItems.querySelectorAll('.cart-item').forEach(row => {
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
