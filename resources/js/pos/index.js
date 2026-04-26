const state = {
    cart: [],
    lastSearch: [],
    weightKg: null,
    isSaving: false,
    lastSaleId: null,
};

function money(value) {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
}

function number(value, decimals = 3) {
    return Number(value || 0).toFixed(decimals);
}

function unitTypeLabel(unitType) {
    return unitType === 'weight' ? 'Peso' : 'Pieza';
}

function cartSubtotal() {
    return state.cart.reduce((sum, line) => sum + line.quantity * line.unitPrice, 0);
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function render() {
    const tbody = document.getElementById('pos-cart-body');
    const empty = document.getElementById('pos-cart-empty');
    const hasItems = state.cart.length > 0;

    if (empty) empty.classList.toggle('hidden', hasItems);
    if (!tbody) return;

    tbody.innerHTML = '';

    for (const [index, line] of state.cart.entries()) {
        const tr = document.createElement('tr');
        tr.className = 'border-b border-gray-100 last:border-b-0 dark:border-gray-800';

        const nameTd = document.createElement('td');
        nameTd.className = 'py-2 pr-2 align-top';
        nameTd.innerHTML = `<div class="font-medium text-gray-900 dark:text-white">${escapeHtml(line.product.name)}</div>
<div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(unitTypeLabel(line.product.unit_type))} · ${escapeHtml(line.product.code || line.product.barcode || '')}</div>`;

        const qtyTd = document.createElement('td');
        qtyTd.className = 'py-2 px-2 align-top w-32';
        qtyTd.innerHTML = `<input type="number" step="${line.product.unit_type === 'weight' ? '0.001' : '1'}" min="0" class="w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:text-white" value="${line.quantity}">`;
        const qtyInput = qtyTd.querySelector('input');
        qtyInput.addEventListener('input', () => {
            const v = parseFloat(qtyInput.value);
            line.quantity = isFinite(v) && v > 0 ? v : 0;
            updateTotals();
            render();
        });

        const priceTd = document.createElement('td');
        priceTd.className = 'py-2 px-2 align-top w-32';
        priceTd.innerHTML = `<input type="number" step="0.01" min="0" class="w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:text-white" value="${line.unitPrice}">`;
        const priceInput = priceTd.querySelector('input');
        priceInput.addEventListener('input', () => {
            const v = parseFloat(priceInput.value);
            line.unitPrice = isFinite(v) && v > 0 ? v : 0;
            updateTotals();
            render();
        });

        const totalTd = document.createElement('td');
        totalTd.className = 'py-2 px-2 align-top text-right w-32';
        totalTd.textContent = money(round2(line.quantity * line.unitPrice));

        const actionsTd = document.createElement('td');
        actionsTd.className = 'py-2 pl-2 align-top text-right w-24';
        actionsTd.innerHTML = `<button type="button" class="text-sm font-semibold text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-200" data-remove="${index}">Quitar</button>`;

        tr.appendChild(nameTd);
        tr.appendChild(qtyTd);
        tr.appendChild(priceTd);
        tr.appendChild(totalTd);
        tr.appendChild(actionsTd);
        tbody.appendChild(tr);
    }

    tbody.querySelectorAll('button[data-remove]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const index = parseInt(btn.getAttribute('data-remove'), 10);
            if (Number.isInteger(index)) {
                state.cart.splice(index, 1);
                updateTotals();
                render();
            }
        });
    });
}

function updateTotals() {
    const subtotal = round2(cartSubtotal());
    setText('pos-subtotal', money(subtotal));

    const cashInput = document.getElementById('pos-cash-received');
    const cash = cashInput ? parseFloat(cashInput.value) : 0;
    const change = round2(Math.max(0, (isFinite(cash) ? cash : 0) - subtotal));
    setText('pos-change', money(change));
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function round2(v) {
    return Math.round((v + Number.EPSILON) * 100) / 100;
}

function ticketUrlForSale(saleId, { autoprint = false } = {}) {
    const template = document.getElementById('pos-ticket-url-template')?.value;
    if (!template) return null;

    const url = new URL(template.replace(/\/0(\/ticket)$/, `/${saleId}$1`), window.location.origin);
    if (autoprint) url.searchParams.set('autoprint', '1');
    return url.toString();
}

function setModalOpen(open) {
    const modal = document.getElementById('pos-sale-success-modal');
    if (!modal) return;

    if (open) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

function showSaleSuccess({ id, total, change_due }) {
    if (!id) return;

    state.lastSaleId = id;
    setText('pos-success-sale-id', `#${id}`);
    setText('pos-success-total', money(total || 0));
    setText('pos-success-change', money(change_due || 0));
    setModalOpen(true);
}

function addToCart(product, quantity) {
    const qty = quantity ?? (product.unit_type === 'weight' ? (state.weightKg ?? 0) : 1);
    const q = isFinite(qty) && qty > 0 ? qty : 0;
    if (q <= 0) return;

    const existing = state.cart.find((l) => l.product.id === product.id && product.unit_type !== 'weight');
    if (existing) {
        existing.quantity = round2(existing.quantity + q);
    } else {
        state.cart.push({
            product,
            quantity: product.unit_type === 'weight' ? parseFloat(number(q, 3)) : Math.round(q),
            unitPrice: product.price,
        });
    }

    updateTotals();
    render();
}

async function searchProducts(query) {
    const q = String(query || '').trim();
    if (!q) return [];

    const res = await window.axios.get('/pos/api/products/search', { params: { q } });
    const data = res?.data?.data || [];
    state.lastSearch = data;
    return data;
}

function renderSearchResults(results) {
    const container = document.getElementById('pos-search-results');
    if (!container) return;

    container.innerHTML = '';

    if (!results || results.length === 0) {
        container.innerHTML = `<div class="text-sm text-gray-500 py-2">Sin resultados</div>`;
        return;
    }

    for (const product of results) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className =
            'w-full text-left cl-surface cl-surface-hover px-3 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-950';
        btn.innerHTML = `<div class="flex items-start justify-between gap-4">
<div>
<div class="font-medium text-gray-900 dark:text-white">${escapeHtml(product.name)}</div>
<div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(unitTypeLabel(product.unit_type))} · ${escapeHtml(product.code || product.barcode || '')}</div>
</div>
<div class="flex items-center gap-3">
<div class="text-sm font-semibold text-gray-900 dark:text-white">${money(product.price)}</div>
<div class="cl-chip">Agregar</div>
</div>
</div>`;
        btn.addEventListener('click', () => addToCart(product));
        container.appendChild(btn);
    }
}

async function refreshWeight() {
    try {
        const res = await window.axios.get('/pos/api/scale/weight');
        const weight = res?.data?.weight;
        state.weightKg = typeof weight === 'number' ? weight : null;
    } catch (e) {
        state.weightKg = null;
    }
    const label = state.weightKg === null ? '--' : `${number(state.weightKg, 3)} kg`;
    setText('pos-weight', label);
}

async function checkout() {
    if (state.isSaving) return;

    const branchId = parseInt(document.getElementById('pos-branch-id')?.value, 10);
    const registerId = parseInt(document.getElementById('pos-register-id')?.value, 10);

    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
    const cashInput = document.getElementById('pos-cash-received');
    const cashReceived = cashInput && cashInput.value !== '' ? parseFloat(cashInput.value) : null;

    const items = state.cart.map((line) => ({
        product_id: line.product.id,
        quantity: line.quantity,
        unit_price: line.unitPrice,
    }));

    if (!Number.isInteger(branchId) || branchId <= 0 || !Number.isInteger(registerId) || registerId <= 0) {
        alert('Configura sucursal y caja.');
        return;
    }

    state.isSaving = true;
    setDisabled(true);

    try {
        const res = await window.axios.post('/pos/api/sales', {
            branch_id: branchId,
            register_id: registerId,
            payment_method: paymentMethod,
            cash_received: cashReceived,
            items,
        });

        const data = res?.data?.data;
        state.cart = [];
        updateTotals();
        render();

        if (data?.id) {
            setText('pos-last-sale', `Venta #${data.id} · Total ${money(data.total)} · Cambio ${money(data.change_due || 0)}`);
            showSaleSuccess(data);
        } else {
            setText('pos-last-sale', 'Venta registrada');
        }
    } catch (e) {
        const message = e?.response?.data?.message || 'No se pudo guardar la venta.';
        alert(message);
    } finally {
        state.isSaving = false;
        setDisabled(false);
    }
}

function setDisabled(disabled) {
    document.querySelectorAll('[data-pos-disable-on-save]').forEach((el) => {
        el.disabled = disabled;
    });
}

async function init() {
    const searchInput = document.getElementById('pos-search');
    const searchBtn = document.getElementById('pos-search-btn');
    const weightBtn = document.getElementById('pos-weight-btn');
    const checkoutBtn = document.getElementById('pos-checkout-btn');
    const cashInput = document.getElementById('pos-cash-received');
    const printBtn = document.getElementById('pos-success-print-btn');
    const modal = document.getElementById('pos-sale-success-modal');

    if (cashInput) {
        cashInput.addEventListener('input', updateTotals);
    }

    if (modal) {
        modal.querySelectorAll('[data-close-modal]').forEach((el) => {
            el.addEventListener('click', () => setModalOpen(false));
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') setModalOpen(false);
        });
    }

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            if (!state.lastSaleId) return;
            const url = ticketUrlForSale(state.lastSaleId, { autoprint: true });
            if (!url) return;

            const w = window.open(url, '_blank', 'noopener,noreferrer');
            if (!w) window.location.href = url;
        });
    }

    if (weightBtn) {
        weightBtn.addEventListener('click', async () => {
            await refreshWeight();
        });
    }

    async function doSearchAndRender() {
        const q = searchInput ? searchInput.value : '';
        const results = await searchProducts(q);
        renderSearchResults(results);

        if (results.length === 1 && (results[0].code === q || results[0].barcode === q)) {
            addToCart(results[0]);
            if (searchInput) searchInput.value = '';
            renderSearchResults([]);
        }
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', doSearchAndRender);
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', async (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                await doSearchAndRender();
            }
        });
    }

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', checkout);
    }

    await refreshWeight();
    const pollMs = parseInt(document.getElementById('pos-scale-poll-ms')?.value || '', 10);
    if (Number.isInteger(pollMs) && pollMs >= 250) {
        window.setInterval(async () => {
            await refreshWeight();
        }, pollMs);
    }
    updateTotals();
    render();
}

init();
