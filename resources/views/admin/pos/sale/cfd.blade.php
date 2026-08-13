<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display - Nexora POS</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <!-- Custom POS CSS -->
    <link rel="stylesheet" href="{{ asset('admin/pos/custome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: #F0F4F8;
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* No scroll for CFD */
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        .cfd-left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: linear-gradient(135deg, #0058A6 0%, #003F7D 100%);
            color: white;
            text-align: center;
        }
        
        .cfd-left-panel h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
        }
        
        .cfd-left-panel p {
            font-size: 24px;
            opacity: 0.8;
        }

        .cfd-right-panel {
            width: 450px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .cfd-cart-header {
            padding: 30px;
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }
        .cfd-cart-header h2 {
            color: #0058A6;
            font-weight: 800;
            margin: 0;
        }

        .cfd-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }
        
        .cfd-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px dashed #E2E8F0;
            font-size: 18px;
            color: #2D3748;
            font-weight: 600;
        }
        .cfd-item-details { width: 60%; }
        .cfd-item-qty { width: 10%; text-align: center; color: #718096; }
        .cfd-item-price { width: 30%; text-align: right; }

        .cfd-cart-footer {
            padding: 30px;
            background: #F8FAFC;
            border-top: 2px solid #E2E8F0;
        }

        .cfd-grand-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 900;
            color: #1A202C;
            font-size: 32px;
        }

        /* Pulse animation when total changes */
        @keyframes pulseTotal {
            0% { transform: scale(1); color: #1A202C; }
            50% { transform: scale(1.05); color: #0058A6; }
            100% { transform: scale(1); color: #1A202C; }
        }
        .pulse-anim {
            animation: pulseTotal 0.5s ease;
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #A0AEC0;
        }
        .empty-state i { font-size: 64px; margin-bottom: 20px; color: #CBD5E0;}
        .empty-state p { font-size: 20px; font-weight: 500; }
    </style>
</head>
<body>

    <div class="cfd-left-panel">
        <h1>Nexora POS</h1>
        <p>Silakan periksa detail pesanan Anda di sebelah kanan.</p>
        <div style="margin-top: 50px;">
            <i class="fas fa-shopping-bag" style="font-size: 120px; opacity: 0.2;"></i>
        </div>
    </div>

    <div class="cfd-right-panel">
        <div class="cfd-cart-header">
            <h2>Detail Pesanan</h2>
        </div>

        <div class="cfd-cart-items" id="cfdCartItems">
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <p>Belum ada pesanan</p>
            </div>
        </div>

        <div class="cfd-cart-footer">
            <div style="display:flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px; color:#718096; font-weight:600;">
                <span>Sub-Total</span>
                <span id="cfdSubtotal">Rp 0</span>
            </div>
            <div style="display:flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px; color:#718096; font-weight:600;">
                <span>Pajak & Diskon</span>
                <span id="cfdTax">...</span>
            </div>
            <div class="cfd-grand-total" id="cfdGrandTotalContainer">
                <span>Total</span>
                <span id="cfdTotal">Rp 0</span>
            </div>
        </div>
    </div>

    <script>
        const cashierId = "{{ $cashier_id }}";
        const apiUrl = `/cfd/api/data/${cashierId}`;
        
        const cartItemsContainer = document.getElementById('cfdCartItems');
        const subtotalEl = document.getElementById('cfdSubtotal');
        const taxEl = document.getElementById('cfdTax');
        const totalEl = document.getElementById('cfdTotal');
        const grandTotalContainer = document.getElementById('cfdGrandTotalContainer');
        
        let lastUpdatedAt = null;

        function formatCurrency(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num || 0);
        }

        async function fetchCartData() {
            try {
                const response = await fetch(apiUrl);
                if (!response.ok) return;
                
                const data = await response.json();
                
                // If data hasn't changed, don't re-render
                if (data.updated_at === lastUpdatedAt) return;
                lastUpdatedAt = data.updated_at;

                renderCart(data.cart);
                renderTotals(data.totals);
                
                // Trigger animation
                grandTotalContainer.classList.remove('pulse-anim');
                void grandTotalContainer.offsetWidth; // trigger reflow
                grandTotalContainer.classList.add('pulse-anim');

            } catch (error) {
                console.error("Error fetching CFD data:", error);
            }
        }

        function renderCart(cartObj) {
            const keys = Object.keys(cartObj || {});
            if (keys.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Belum ada pesanan</p>
                    </div>`;
                return;
            }

            let html = '';
            keys.forEach(key => {
                const item = cartObj[key];
                const itemTotal = item.price * item.quantity;
                html += `
                    <div class="cfd-item">
                        <div class="cfd-item-details">
                            ${item.product_name}
                            ${item.category_name ? `<div style="font-size:14px; color:#A0AEC0;">${item.category_name}</div>` : ''}
                        </div>
                        <div class="cfd-item-qty">x${item.quantity}</div>
                        <div class="cfd-item-price">${formatCurrency(itemTotal)}</div>
                    </div>
                `;
            });
            cartItemsContainer.innerHTML = html;
            
            // Auto scroll to bottom
            cartItemsContainer.scrollTop = cartItemsContainer.scrollHeight;
        }

        function renderTotals(totalsObj) {
            if (!totalsObj) return;
            subtotalEl.textContent = formatCurrency(totalsObj.subtotal);
            
            // Just for display simplicity, show tax - discount if any
            let taxStr = "";
            if (totalsObj.tax > 0) taxStr += `+ ${formatCurrency(totalsObj.tax)} `;
            if (totalsObj.discount > 0) taxStr += `- ${formatCurrency(totalsObj.discount)}`;
            if (taxStr === "") taxStr = "Rp 0";
            
            taxEl.textContent = taxStr;
            totalEl.textContent = formatCurrency(totalsObj.total);
        }

        // Poll every 1.5 seconds
        setInterval(fetchCartData, 1500);
        
        // Initial fetch
        fetchCartData();
    </script>
</body>
</html>
