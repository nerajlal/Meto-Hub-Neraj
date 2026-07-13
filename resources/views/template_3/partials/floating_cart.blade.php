<div class="floating-cart-pill" id="global-floating-cart" onclick="toggleCartSidebar()">
    <div class="floating-cart-images" id="floating-cart-images">
        <!-- Will be populated dynamically by JS. Example:
        <img src="path/to/img1.jpg">
        <img src="path/to/img2.jpg">
        -->
    </div>
    <div class="floating-cart-text">
        <span class="floating-cart-title">VIEW CART</span>
        <span class="floating-cart-count" id="floating-cart-count">0 items</span>
    </div>
    <div class="floating-cart-arrow">
        <i class="fa-solid fa-chevron-right"></i>
    </div>
</div>

<script>
    // Example JS logic to show/hide the floating cart pill
    // This should ideally hook into the main cart update logic
    function updateFloatingCartPill(itemCount, recentImages) {
        const pill = document.getElementById('global-floating-cart');
        const countSpan = document.getElementById('floating-cart-count');
        const imagesContainer = document.getElementById('floating-cart-images');
        
        if (itemCount > 0) {
            countSpan.innerText = itemCount + (itemCount === 1 ? ' item' : ' items');
            
            // Update images
            imagesContainer.innerHTML = '';
            recentImages.slice(0, 3).forEach(img => {
                const imgEl = document.createElement('img');
                imgEl.src = img;
                imagesContainer.appendChild(imgEl);
            });
            
            pill.classList.add('visible');
            
            // Auto hide after 5 seconds of inactivity (optional behavior)
            clearTimeout(window.floatingCartTimeout);
            window.floatingCartTimeout = setTimeout(() => {
                pill.classList.remove('visible');
            }, 5000);
            
        } else {
            pill.classList.remove('visible');
        }
    }
</script>
