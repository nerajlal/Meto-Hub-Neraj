(() => {
    const root = document.querySelector('[data-bundle-picker]');
    if (!root) return;
    const {products, rows, currency} = window.bundlePickerData;
    const container = root.querySelector('[data-bundle-rows]');
    const search = root.querySelector('input[type=search]');
    const results = root.querySelector('[data-bundle-results]');
    const discountType = document.querySelector('[name=discount_type]');
    const discountValue = document.querySelector('[name=discount_value]');
    const money = value => currency + ' ' + Number(value).toFixed(2);
    let nextId = 0;
    function summary() {
        let total = 0, count = 0;
        container.querySelectorAll('[data-bundle-item]').forEach(row => {
            const option = row.querySelector('select').selectedOptions[0];
            const quantity = Number(row.querySelector('[name="quantities[]"]').value) || 0;
            total += Number(option?.dataset.price || 0) * quantity;
            count += quantity;
        });
        const discount = Number(discountValue.value) || 0;
        total -= discountType.value === 'percentage' ? total * discount / 100 : discount;
        document.getElementById('summary_count').textContent = count;
        document.getElementById('summary_total').textContent = money(Math.max(0, total));
    }
    function add(product, variantId = '', quantity = 1) {
        const row = document.createElement('div');
        row.dataset.bundleItem = '';
        row.className = 'border rounded p-3';
        const id = 'bundle-option-' + nextId++;
        // Only static markup goes through innerHTML; merchant content uses textContent.
        row.innerHTML = '<div class="d-flex justify-content-between gap-2 mb-2"><strong data-title></strong><button type="button" class="btn btn-sm btn-outline-danger" data-remove>Remove</button></div><input type="hidden" name="products[]"><div class="row g-2"><div class="col-12 col-sm-8"><label class="form-label small">Variant</label><select name="variant_ids[]" class="form-select" required></select></div><div class="col-12 col-sm-4"><label class="form-label small">Quantity in bundle</label><input type="number" name="quantities[]" class="form-control" min="1" max="10000" required></div></div>';
        row.querySelector('[data-title]').textContent = product.title + (product.active ? '' : ' (inactive — remove or activate this product)');
        row.querySelector('[name="products[]"]').value = product.id;
        const select = row.querySelector('select');
        select.id = id;
        select.previousElementSibling.htmlFor = id;
        const qty = row.querySelector('[name="quantities[]"]');
        qty.id = id + '-qty'; qty.previousElementSibling.htmlFor = qty.id; qty.value = quantity;
        select.add(new Option('Choose variant', ''));
        product.variants.forEach(variant => {
            const option = new Option(variant.label + ' — ' + money(variant.price) + (variant.stock <= 0 ? ' (out of stock)' : ''), variant.id);
            option.dataset.price = variant.price;
            select.add(option);
        });
        select.value = variantId || (product.variants.length === 1 ? product.variants[0].id : '');
        row.querySelector('[data-remove]').addEventListener('click', () => { row.remove(); summary(); });
        select.addEventListener('change', summary);
        qty.addEventListener('input', summary);
        container.append(row);
        summary();
    }
    search.addEventListener('input', () => {
        results.replaceChildren();
        const query = search.value.trim().toLowerCase();
        results.hidden = !query;
        if (!query) return;
        const matches = products.filter(product => product.title.toLowerCase().includes(query));
        matches.forEach(product => {
            const button = document.createElement('button');
            button.type = 'button'; button.className = 'list-group-item list-group-item-action';
            button.textContent = product.title + (product.variants.length ? '' : ' — no variants available');
            button.disabled = !product.variants.length || !product.active;
            button.addEventListener('click', () => { add(product); search.value = ''; results.hidden = true; });
            results.append(button);
        });
        if (!matches.length) {
            const empty = document.createElement('p'); empty.className = 'small text-muted p-2'; empty.textContent = 'No matching products.'; results.append(empty);
        }
    });
    rows.forEach(row => { const product = products.find(product => Number(product.id) === Number(row.product)); if (product) add(product, row.variant, row.quantity); });
    discountType.addEventListener('change', summary);
    discountValue.addEventListener('input', summary);
    summary();
})();
