<!-- Floating Cart Summary Pill -->
<div id="floating-cart-summary" class="shadow-lg" style="display: none; position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%); width: auto; min-width: 280px; max-width: 90%; background: #ffffff; color: var(--primary-color); border-radius: 50px; padding: 10px 16px 10px 10px; z-index: 999; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border-color);" onclick="toggleNCart(true)">
    
    <div style="display: flex; align-items: center; gap: 12px;">
        <!-- Stacked Images Container -->
        <div id="floating-cart-images" style="display: flex; align-items: center; margin-right: 4px;">
            <!-- Images will be injected here via JS -->
        </div>
        
        <!-- Text Info -->
        <div style="display: flex; flex-direction: column; line-height: 1.1;">
            <span style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary-color);">View Cart</span>
            <span id="floating-cart-count-text" style="font-size: 0.75rem; font-weight: 500; color: var(--text-muted); margin-top: 2px;">0 items</span>
        </div>
    </div>
    
    <div style="display: flex; align-items: center; margin-left: 20px;">
        <i class="fa-solid fa-chevron-right" style="font-size: 0.9rem; color: #94a3b8;"></i>
    </div>
</div>

<script>
    window.cartItemsMap = {};
    window.cartTotalValue = 0;
    window.cartTotalCount = 0;
    window.cartImages = [];
    window.floatingCartTimer = null;
    window.productCardTimers = {};

    function renderCartImages() {
        const container = $('#floating-cart-images');
        container.empty();
        
        if (!window.cartImages || window.cartImages.length === 0) {
            container.append(`
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--accent-color); color: white; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <i class="fa-solid fa-basket-shopping fs-6"></i>
                </div>
            `);
            return;
        }

        let zIndex = 3;
        window.cartImages.forEach((imgUrl, index) => {
            let margin = index > 0 ? '-12px' : '0';
            
            // Generate proper URL
            let fullUrl = imgUrl;
            if (!imgUrl.startsWith('http') && !imgUrl.startsWith('/')) {
                fullUrl = '/' + imgUrl;
            }
            if (fullUrl.startsWith('/public/')) {
                fullUrl = fullUrl.replace('/public/', '/storage/');
            } else if (!fullUrl.startsWith('/storage/') && !fullUrl.startsWith('/Images/') && !imgUrl.startsWith('http')) {
                fullUrl = '/storage' + fullUrl;
            }
            
            container.append(`
                <div style="width: 34px; height: 34px; border-radius: 50%; background: #f8fafc; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-left: ${margin}; z-index: ${zIndex}; overflow: hidden;">
                    <img src="${fullUrl}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            `);
            zIndex--;
        });
    }

    function syncCartUI(showFloatingCart = false) {
        // Sync Floating Cart logic
        if (window.cartTotalCount > 0 && showFloatingCart) {
            renderCartImages();
            $('#floating-cart-count-text').text(window.cartTotalCount + (window.cartTotalCount === 1 ? ' item' : ' items'));
            
            $('#floating-cart-summary').css('display', 'flex').hide().fadeIn();
            
            // Auto hide after 5 seconds
            clearTimeout(window.floatingCartTimer);
            window.floatingCartTimer = setTimeout(() => {
                $('#floating-cart-summary').fadeOut();
            }, 5000);
        } else {
            $('#floating-cart-summary').fadeOut();
        }
    }

    function updateProductCardUI(key, qty, forceHide = false) {
        $('.product-action-wrapper[data-cart-key="' + key + '"]').each(function() {
            if (qty > 0 && !forceHide) {
                $(this).find('.inline-add-btn').hide();
                $(this).find('.qty-controller').css('display', 'flex');
                $(this).find('.qty-value').text(qty);
                
                // Auto hide after 5 seconds
                clearTimeout(window.productCardTimers[key]);
                window.productCardTimers[key] = setTimeout(() => {
                    updateProductCardUI(key, qty, true);
                }, 5000);
            } else {
                $(this).find('.qty-controller').hide();
                $(this).find('.inline-add-btn').css('display', 'flex');
                clearTimeout(window.productCardTimers[key]);
            }
        });
    }

    // Call on load (syncs product cards but DOES NOT show floating cart)
    $(document).ready(function() {
        $.get("{{ route('cart.state') }}", function(response) {
            if (response.success) {
                window.cartItemsMap = response.cartItemsMap || {};
                window.cartTotalCount = response.cartCount;
                window.cartTotalValue = response.cartTotal;
                window.cartImages = response.cartImages || [];
                syncCartUI(false); // don't show floating on initial load
            }
        });
    });

    // Custom inline cart updater
    window.updateInlineCart = function(key, delta) {
        let currentQty = window.cartItemsMap[key] || 0;
        let newQty = currentQty + delta;
        
        if (newQty <= 0) {
            // Remove item
            $.post("{{ route('cart.remove') }}", {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: key
            }, function(response) {
                if(response.success) {
                    window.cartItemsMap = response.cartItemsMap || {};
                    window.cartTotalCount = response.cartCount;
                    window.cartTotalValue = response.cartTotal;
                    window.cartImages = response.cartImages || [];
                    $('#cart-count').text(response.cartCount);
                    updateProductCardUI(key, 0); // Hide qty selector
                    syncCartUI(true); // show floating cart
                    refreshNCart(); // update side drawer if open
                }
            });
        } else {
            // Either Add or Update based on if it existed
            if (currentQty === 0) {
                // Determine if it's bundle or product
                let parts = key.toString().split('-');
                let isBundle = parts[0] === 'bundle';
                let reqData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: isBundle ? parts[1] : parts[0],
                    quantity: newQty,
                    size: (!isBundle && parts.length > 1) ? parts.slice(1).join('-') : null,
                    type: isBundle ? 'bundle' : 'product'
                };
                
                $.post("{{ route('cart.add') }}", reqData, function(response) {
                    if (response.success) {
                        window.cartItemsMap = response.cartItemsMap || {};
                        window.cartTotalCount = response.cartCount;
                        window.cartTotalValue = response.cartTotal;
                        window.cartImages = response.cartImages || [];
                        $('#cart-count').text(response.cartCount);
                        updateProductCardUI(key, newQty); // Show qty selector
                        syncCartUI(true); // show floating cart
                        refreshNCart();
                    } else {
                        showCartToast(response.message || 'Error adding item');
                    }
                }).fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showCartToast(xhr.responseJSON.message);
                    } else {
                        showCartToast('Error adding item');
                    }
                });
            } else {
                // Update
                $.post("{{ route('cart.update') }}", {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: key,
                    quantity: newQty
                }, function(response) {
                    if(response.success) {
                        window.cartItemsMap = response.cartItemsMap || {};
                        window.cartTotalCount = response.cartCount;
                        window.cartTotalValue = response.cartTotal;
                        window.cartImages = response.cartImages || [];
                        $('#cart-count').text(response.cartCount);
                        updateProductCardUI(key, newQty); // Update qty selector value
                        syncCartUI(true); // show floating cart
                        refreshNCart();
                    } else {
                        showCartToast(response.message || 'Error updating item');
                    }
                }).fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showCartToast(xhr.responseJSON.message);
                    } else {
                        showCartToast('Error updating item');
                    }
                });
            }
        }
    };
</script>
