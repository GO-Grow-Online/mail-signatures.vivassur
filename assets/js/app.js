jQuery(function ($) {

    const PROD_BASE_URL = 'https://www.vivassur.be/mail-signature';

    // Image fallback for local DDEV preview:
    // If remote files on vivassur.be return 404 (before deployment),
    // display local images in the browser preview while preserving the production URL for copy.
    $('.signature-card__signature img').each(function () {
        const $img = $(this);
        const originalSrc = $img.attr('src') || '';

        $img.attr('data-prod-src', originalSrc);

        $img.on('error', function () {
            if (originalSrc.includes('vivassur.be/mail-signature/assets')) {
                const localSrc = originalSrc.replace('https://www.vivassur.be/mail-signature/assets', window.location.origin + '/things/assets');
                $img.off('error').attr('src', localSrc);
            }
        });
    });

    // Extract clean production HTML with https://www.vivassur.be/mail-signature URLs
    function getProductionHtml(container) {
        const clone = container.cloneNode(true);
        const imgs = clone.querySelectorAll('img');
        
        imgs.forEach(img => {
            const prodSrc = img.getAttribute('data-prod-src');
            if (prodSrc) {
                img.setAttribute('src', prodSrc);
                img.removeAttribute('data-prod-src');
            } else {
                let src = img.getAttribute('src') || '';
                if (!src.startsWith(PROD_BASE_URL)) {
                    src = src.replace(/^https?:\/\/[^\/]+/, PROD_BASE_URL);
                    img.setAttribute('src', src);
                }
            }
        });

        return clone.innerHTML.trim();
    }

    // Show sleek toast notification
    function showToast(message, isSuccess = true) {
        const $toast = $('#toast');
        $toast.text(message)
              .removeClass('toast--error')
              .addClass('toast--visible');
        
        if (!isSuccess) {
            $toast.addClass('toast--error');
        }

        setTimeout(() => {
            $toast.removeClass('toast--visible');
        }, 3500);
    }

    // Temporary button state change
    function animateButton($btn, successText = 'Copié !') {
        const originalHtml = $btn.html();
        $btn.addClass('btn--copied')
            .html(`
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>${successText}</span>
            `);
        
        setTimeout(() => {
            $btn.removeClass('btn--copied').html(originalHtml);
        }, 2200);
    }

    // Copy rich signature (for direct paste into mail clients)
    $('.btn--copy').on('click', async function (e) {
        e.preventDefault();
        const $btn = $(this);
        const targetId = $btn.attr('data-id');
        const container = document.getElementById(targetId);

        if (!container) {
            showToast("Impossible de trouver la signature.", false);
            return;
        }

        const prodHtml = getProductionHtml(container);
        let copied = false;

        // 1. Try modern Async Clipboard API with text/html blob
        if (navigator.clipboard && window.ClipboardItem) {
            try {
                const htmlBlob = new Blob([prodHtml], { type: 'text/html' });
                const textBlob = new Blob([container.innerText], { type: 'text/plain' });
                await navigator.clipboard.write([
                    new ClipboardItem({
                        'text/html': htmlBlob,
                        'text/plain': textBlob
                    })
                ]);
                copied = true;
            } catch (err) {
                console.warn('Async clipboard write failed, trying fallback:', err);
            }
        }

        // 2. Fallback to temporary hidden div + createRange + execCommand
        if (!copied) {
            try {
                const tempDiv = document.createElement('div');
                tempDiv.style.position = 'fixed';
                tempDiv.style.left = '-9999px';
                tempDiv.innerHTML = prodHtml;
                document.body.appendChild(tempDiv);

                const range = document.createRange();
                range.selectNode(tempDiv);
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
                copied = document.execCommand('copy');
                selection.removeAllRanges();

                document.body.removeChild(tempDiv);
            } catch (err) {
                console.error('execCommand copy failed:', err);
            }
        }

        if (copied) {
            animateButton($btn, 'Signature copiée !');
            showToast('✓ Signature copiée ! Prête à coller dans votre boîte mail (Ctrl+V / Cmd+V).');
        } else {
            showToast('Oups, impossible de copier automatiquement. Veuillez sélectionner la signature manuellement.', false);
        }
    });

    // Copy raw HTML code
    $('.btn--copy-html').on('click', async function (e) {
        e.preventDefault();
        const $btn = $(this);
        const targetId = $btn.attr('data-id');
        const container = document.getElementById(targetId);

        if (!container) return;

        const prodHtml = getProductionHtml(container);

        try {
            if (navigator.clipboard) {
                await navigator.clipboard.writeText(prodHtml);
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = prodHtml;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            }
            animateButton($btn, 'Code copié !');
            showToast('✓ Code HTML public (vivassur.be/mail-signature) copié !');
        } catch (err) {
            showToast('Erreur lors de la copie du code HTML.', false);
        }
    });

});