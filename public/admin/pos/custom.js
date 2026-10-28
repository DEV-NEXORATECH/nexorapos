document.addEventListener('DOMContentLoaded', function () {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    const productGrid = document.getElementById('productGrid');
    const categoryFilter = document.getElementById('categoryFilter');
    const productSearch = document.getElementById('productSearch');
    const cartItems = document.getElementById('cartItems');
    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discount');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');

    // ------------------------
    // ✅ Notification function
    // ------------------------
    function showNotification(type, message) {
        if (window.NexoraPopup) {
            if (type === 'error') {
                window.NexoraPopup.error({
                    title: 'POS Error',
                    message,
                    detail: 'Aksi POS gagal diproses. Cek item, metode bayar, atau coba ulang.',
                });
            } else {
                window.NexoraPopup.success({
                    title: type === 'info' ? 'POS Updated' : 'POS Success',
                    message,
                    detail: 'Cart dan total transaksi sudah diperbarui.',
                    timer: 1800,
                });
            }
            return;
        }

        const el = document.querySelector(`.notification.${type}`);
        if (!el) return;

        el.textContent = message;
        el.style.display = "block";
        el.classList.add("show");

        setTimeout(() => {
            el.style.display = "none";
            el.classList.remove("show");
        }, 2500);
    }

    // Render cart items
    function renderCart(cart) {
        if (window.renderPosCart) {
            window.renderPosCart(cart);
            return;
        }
        if (!cartItems) return;
        cartItems.innerHTML = '';
        Object.keys(cart || {}).forEach(key => {
            const item = cart[key];
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center justify-content-between mb-2';
            div.innerHTML = `
                <div>
                    <div><strong>${item.product_name}</strong> ${item.category_name ? '(' + item.category_name + ')' : ''}</div>
                    <small>Price: ${Number(item.price).toFixed(2)} | Tax: ${Number(item.tax_percent).toFixed(2)}%</small>
                </div>
                <div class="d-flex align-items-center">
                    <input type="number" min="1" value="${item.quantity}" data-key="${key}" class="form-control form-control-sm qty-change" style="width:80px;">
                    <button class="btn btn-danger btn-sm ml-2 remove-item" data-key="${key}">x</button>
                </div>
            `;
            cartItems.appendChild(div);
        });
    }

    // Update totals
    function updateTotals(t) {
        if (window.updatePosTotals) {
            window.updatePosTotals(t);
            return;
        }
        if (!subtotalEl || !discountEl || !taxEl || !totalEl) return;
        subtotalEl.textContent = Number(t.subtotal).toFixed(2);
        discountEl.textContent = Number(t.discount).toFixed(2);
        taxEl.textContent = Number(t.tax).toFixed(2);
        totalEl.textContent = Number(t.total).toFixed(2);
        calculateChange();
    }

    function calculateChange() {
        const total = parseFloat(totalEl?.textContent || 0);
        const paidInput = document.getElementById('paidAmount');
        const changeInput = document.getElementById('changeAmount');
        if (paidInput && changeInput) {
            const paid = parseFloat(paidInput.value || 0);
            const change = paid - total;
            changeInput.value = change > 0 ? change.toFixed(2) : "0.00";
        }
    }

    const paidAmountInput = document.getElementById('paidAmount');
    if (paidAmountInput) {
        paidAmountInput.addEventListener('input', calculateChange);
    }

    // Add product by clicking on card
    if (productGrid) {
        productGrid.addEventListener('click', async function (e) {
            const card = e.target.closest('.clickable-card');
            if (!card) return;

            const productId = card.getAttribute('data-id');
            const price = card.getAttribute('data-price');
            const variantSelect = card.querySelector('.variant-select');
            const product_variant_id = variantSelect ? variantSelect.value : null;

            try {
                const res = await fetch(`/admin-panel/pos/cart/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        product_variant_id,
                        quantity: 1,
                        price
                    })
                });
                const data = await res.json();
                if (data.success) {
                    renderCart(data.cart);
                    updateTotals(data.totals);

                    // flash border
                    card.style.border = "2px solid #0058A6";
                    setTimeout(() => card.style.border = "", 500);

                    // ✅ Notification
                    showNotification("success", "Product added to cart!");
                } else {
                    showNotification("error", data.message || "Failed to add to cart.");
                }
            } catch (err) {
                console.error(err);
                showNotification("error", "Error adding product to cart.");
            }
        });
    }

    // Update quantity
    if (cartItems) {
        cartItems.addEventListener('change', async function (e) {
            if (e.target.classList.contains('qty-change')) {
                const key = e.target.getAttribute('data-key');
                const quantity = Number(e.target.value || 1);
                try {
                    const res = await fetch(`/admin-panel/pos/cart/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ key, quantity })
                    });
                    const data = await res.json();
                    if (data.success) {
                        renderCart(data.cart);
                        updateTotals(data.totals);
                        showNotification("info", "Cart updated.");
                    } else {
                        showNotification("error", data.message || "Failed to update cart.");
                    }
                } catch (err) {
                    console.error(err);
                    showNotification("error", "Error updating cart.");
                }
            }
        });

        // Remove item
        cartItems.addEventListener('click', async function (e) {
            if (e.target.classList.contains('remove-item')) {
                const key = e.target.getAttribute('data-key');
                try {
                    const res = await fetch(`/admin-panel/pos/cart/remove`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ key })
                    });
                    const data = await res.json();
                    if (data.success) {
                        renderCart(data.cart);
                        updateTotals(data.totals);
                        showNotification("info", "Product removed.");
                    } else {
                        showNotification("error", data.message || "Failed to remove item.");
                    }
                } catch (err) {
                    console.error(err);
                    showNotification("error", "Error removing item.");
                }
            }
        });
    }

    // Checkout
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', async function () {
            const customerInput = document.getElementById('customerId');
            const storeInput = document.getElementById('storeId');
            const methodInput = document.getElementById('methodId');
            const customer_id = customerInput ? customerInput.value : null;
            const store_id = storeInput ? storeInput.value : null;
            const method_id = methodInput ? methodInput.value : null;
            const paidInput = document.getElementById('paidAmount');
            const paid_amount = paidInput ? parseFloat(paidInput.value || 0) : 0;
            const changeInput = document.getElementById('changeAmount');
            const change_amount = changeInput ? parseFloat(changeInput.value || 0) : 0;

            try {
                const res = await fetch(`/admin-panel/pos/checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ customer_id, store_id, method_id, paid_amount, change_amount })
                });

                const data = await res.json();
                if (data.success && data.redirect) {
                    showNotification("success", "Checkout successful!");
                    window.location.href = data.redirect;
                } else {
                    showNotification("error", data.message || "Checkout failed.");
                }
            } catch (err) {
                console.error(err);
                showNotification("error", "Error during checkout.");
            }
        });
    }

    // Filters
    function filterCards() {
        const catId = categoryFilter ? categoryFilter.value : '';
        const q = (productSearch && productSearch.value || '').toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const matchesCat = !catId || card.getAttribute('data-category') === catId;
            const matchesText = !q || card.getAttribute('data-name').toLowerCase().includes(q);
            card.style.display = (matchesCat && matchesText) ? '' : 'none';
        });
    }

    if (categoryFilter) categoryFilter.addEventListener('change', filterCards);
    if (productSearch) productSearch.addEventListener('input', filterCards);
});

$(document).ready(function() {
    $('.print').on('click', function(e) {
        e.preventDefault();
        window.print();
    });
});
