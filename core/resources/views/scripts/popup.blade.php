<style>
    /* Overlay */
    .custom-popup-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    }

    /* Popup Box */
    .custom-popup {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        width: 90%;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        animation: popupFadeIn 0.4s ease;
    }

    @keyframes popupFadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .custom-popup img {
        width: 100%;
        display: block;
    }

    .custom-popup-content {
        padding: 15px 20px;
        color: #333;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
    }

    /* CTA Button (Click Button) */
    .custom-popup .popup-btn {
        display: inline-block;
        margin: 10px auto 15px;
        background: var(--theme-color, #ff0000);
        color: #fff;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
    }

    .custom-popup .popup-btn:hover {
        background: color-mix(in srgb, var(--theme-color, #ff0000) 80%, black);
    }

    /* CLOSE Button (like RedTopUpBD style) */
    .popup-close-btn {
        width: calc(100% - 20px);
        margin: 0 auto 15px;
        display: block;
        background: var(--theme-color, #ff0000);
        color: #fff;
        border: none;
        padding: 12px 0;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .popup-close-btn:hover {
        background: color-mix(in srgb, var(--theme-color, #ff0000) 80%, black);
    }
</style>

<script>
    $(document).ready(function () {

        function showPopup(url, image_url, content, button_text) {
            const popupHtml = `
                <div class="custom-popup-overlay">
                    <div class="custom-popup">
                        ${image_url ? `<img src="${image_url}" alt="Popup Image">` : ''}
                        <div class="custom-popup-content">
                            ${content || ''}
                            ${url ? `<a href="${url}" class="popup-btn">${button_text || 'Click Here'}</a>` : ''}
                        </div>
                        <button class="popup-close-btn">✘ CLOSE</button>
                    </div>
                </div>
            `;

            $('body').append(popupHtml);

            $('.popup-close-btn').on('click', function () {
                $('.custom-popup-overlay').fadeOut(300, function () {
                    $(this).remove();
                });
            });
        }

        function fetchPopups() {
            const popupRoute = '{{ route('popup') }}';
            fetch(popupRoute)
                .then(res => res.json())
                .then(data => {
                    const popups = data.popups;
                    popups.forEach(p => {
                        showPopup(p.url, p.image_url, p.content, p.button_text);
                    });
                })
                .catch(err => console.error('Popup fetch error:', err));
        }

        fetchPopups();
    });
</script>
