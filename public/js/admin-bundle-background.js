(() => {
    const root = document.querySelector('[data-bundle-background]');
    if (!root) return;
    const mode = root.querySelector('[name="card_background[mode]"]');
    const first = root.querySelector('[name="card_background[color]"]');
    const second = root.querySelector('[name="card_background[color_end]"]');
    const angle = root.querySelector('[name="card_background[angle]"]');
    const preview = root.querySelector('[data-background-preview]');
    const valid = value => /^#[a-f0-9]{6}$/i.test(value);
    function update() {
        root.querySelector('[data-background-colors]').hidden = mode.value === 'auto';
        root.querySelectorAll('[data-background-gradient]').forEach(element => element.hidden = mode.value !== 'gradient');
        const color = mode.value === 'auto' || !valid(first.value) ? '#fff0bd' : first.value;
        const end = mode.value === 'gradient' && valid(second.value) ? second.value : color;
        const degrees = Math.max(0, Math.min(360, Number(angle.value) || 0));
        preview.style.background = mode.value === 'gradient' ? `linear-gradient(${degrees}deg, ${color}, ${end})` : color;
        const channels = [1,3,5].map(offset => Math.round((parseInt(color.slice(offset,offset+2),16)+parseInt(end.slice(offset,offset+2),16))/2)/255).map(value => value <= .04045 ? value/12.92 : ((value+.055)/1.055)**2.4);
        preview.style.color = channels[0]*.2126+channels[1]*.7152+channels[2]*.0722 > .179 ? '#000000' : '#ffffff';
    }
    root.querySelectorAll('[data-color-picker]').forEach(picker => {
        const text = picker.dataset.colorPicker === 'color' ? first : second;
        picker.addEventListener('input', () => { text.value = picker.value; update(); });
        text.addEventListener('input', () => { if(valid(text.value)) picker.value = text.value; update(); });
    });
    mode.addEventListener('change', update);
    angle.addEventListener('input', update);
    document.querySelector('[name=title]')?.addEventListener('input', event => root.querySelector('[data-background-title]').textContent = event.target.value || 'Your bundle');
    update();
})();
