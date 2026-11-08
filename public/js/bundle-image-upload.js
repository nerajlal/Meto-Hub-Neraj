(() => {
    'use strict';
    document.querySelectorAll('form input[type="file"][name="image"]').forEach(input => {
        const form = input.form;
        const message = document.createElement('p');
        message.className = 'form-text mb-0';
        message.setAttribute('role', 'status');
        input.after(message);
        let processing = false;
        form.addEventListener('submit', async event => {
            const file = input.files[0];
            if (!file || file.type === 'image/webp') return;
            event.preventDefault();
            if (processing) return;
            if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > 5 * 1024 * 1024) {
                message.textContent = 'Choose a JPG, PNG or WebP image up to 5 MB.';
                input.focus();
                return;
            }
            processing = true;
            message.textContent = 'Preparing WebP image…';
            const url = URL.createObjectURL(file);
            try {
                const image = new Image();
                await new Promise((resolve, reject) => { image.onload = resolve; image.onerror = reject; image.src = url; });
                const scale = Math.min(1, 1600 / Math.max(image.naturalWidth, image.naturalHeight));
                const canvas = document.createElement('canvas');
                canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
                canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
                canvas.getContext('2d').drawImage(image, 0, 0, canvas.width, canvas.height);
                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', .85));
                if (!blob || blob.type !== 'image/webp') throw new Error('WebP conversion unavailable');
                const transfer = new DataTransfer();
                transfer.items.add(new File([blob], file.name.replace(/\.[^.]+$/, '') + '.webp', {type: 'image/webp'}));
                input.files = transfer.files;
                message.textContent = 'WebP image ready.';
                processing = false;
                form.requestSubmit(event.submitter || undefined);
            } catch (_) {
                message.textContent = 'Could not convert this image. Try another image or upload a WebP file.';
                input.focus();
            } finally {
                processing = false;
                URL.revokeObjectURL(url);
            }
        });
    });
})();
