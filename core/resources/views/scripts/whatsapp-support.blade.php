<!-- resources/views/components/whatsapp-support.blade.php -->
<style>
#bwb-help-container {
    position: fixed;
    bottom: 90px;
    right: 20px;
    z-index: 9999;
    pointer-events: none;
}

#bwb-help-inner {
    display: flex;
    align-items: center;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity 0.4s ease, transform 0.4s ease;
    pointer-events: auto;
}

#bwb-help-inner.visible {
    opacity: 1;
    transform: translateY(0);
}

#bwb-help-text {
    background-color: var(--theme-color, #{{ $settings->theme_color ?? 'f29f2c' }});
    color: #fff;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    margin-right: 8px;
    white-space: nowrap;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    font-family: inherit;
    line-height: 1;
}

#bwb-help-btn {
    background-color: var(--theme-color, #{{ $settings->theme_color ?? 'f29f2c' }});
    border-radius: 50%;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    cursor: pointer;
    text-decoration: none !important;
    transition: transform 0.3s ease;
}

#bwb-help-btn:hover {
    transform: scale(1.08);
}
</style>

<div id="bwb-help-container">
    <div id="bwb-help-inner">
        <div id="bwb-help-text">সাহায্য লাগবে ?</div>
        <a id="bwb-help-btn"
           href="https://wa.me/{{ $settings->whatsapp_number ?? '8801572914334' }}"
           target="_blank"
           aria-label="WhatsApp Support">
            <i class="fas fa-phone"></i>
        </a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const wrapper = document.getElementById("bwb-help-inner");

    // Smooth fade-in
    setTimeout(() => wrapper.classList.add("visible"), 150);

    // Set theme color immediately (no flicker)
    const themeColor =
        getComputedStyle(document.documentElement)
            .getPropertyValue("--theme-color")
            .trim() || "#{{ $settings->theme_color ?? 'f29f2c' }}";

    document.querySelectorAll("#bwb-help-text, #bwb-help-btn").forEach(el => {
        el.style.backgroundColor = themeColor;
    });
});
</script>
