<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{ $filePath }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="controls">
                    <button id="zoom-in">🔍 Zoom In</button>
                    <button id="zoom-out">🔎 Zoom Out</button>
                    <button id="fullscreen">⛶ Fullscreen</button>
                    <button id="sound-toggle">🔊 Sound On</button>
                </div>

                <!-- Flipbook -->
                <div id="flipbook-wrapper">
                    <div id="flipbook">Loading...</div>
                    <div class="nav-arrow left" id="prev">◀</div>
                    <div class="nav-arrow right" id="next">▶</div>
                </div>

                <!-- Slider -->
                <input id="page-slider" type="range" min="1" max="1" value="1">

                <!-- Share + Download -->
                <div class="bottom-bar">
                    <button id="share-btn">🔗 Share</button>
                    <a href="{{ asset( $filePath ) }}" download class="download-btn">⬇ Download PDF</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

    const pdfUrl = "{{ asset( $filePath ) }}";
    const bookWidth = 1000,
        bookHeight = 650,
        pageWidth = bookWidth / 2,
        pageHeight = bookHeight;
    let pdfDoc = null,
        userZoom = 1.0,
        soundEnabled = true;
    const flipSound = new Audio('https://www.soundjay.com/buttons/sounds/page-flip-01a.mp3');

    $(function() {
        const $flipbook = $('#flipbook');

        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            $flipbook.empty();
            $('#page-slider').attr('max', pdf.numPages);

            for (let i = 1; i <= pdf.numPages; i++) {
                $flipbook.append('<div class="page"><canvas id="canvas-' + i + '"></canvas></div>');
            }

            $flipbook.turn({
                width: bookWidth,
                height: bookHeight,
                autoCenter: true,
                display: 'double',
                duration: 700,
                elevation: 50,
                gradients: true,
                when: {
                    turning: function(event, page) {
                        renderPage(page);
                        renderPage(page + 1);
                        $('#page-slider').val(page);
                        if (soundEnabled) {
                            try {
                                flipSound.currentTime = 0;
                                flipSound.play();
                            } catch (e) {}
                        }
                    }
                }
            });

            renderPage(1);
            if (pdf.numPages > 1) renderPage(2);
        });

        function renderPage(num) {
            if (!pdfDoc || num < 1 || num > pdfDoc.numPages) return;
            const canvas = document.getElementById('canvas-' + num);
            if (!canvas) return;
            pdfDoc.getPage(num).then(function(page) {
                const unscaled = page.getViewport({
                    scale: 1
                });
                const fitScale = (pageWidth / unscaled.width) * userZoom;
                const viewport = page.getViewport({
                    scale: fitScale
                });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.style.width = pageWidth + 'px';
                canvas.style.height = pageHeight + 'px';
                page.render({
                    canvasContext: canvas.getContext('2d'),
                    viewport
                });
            });
        }

        function reloadPages() {
            const current = $flipbook.turn('page') || 1;
            for (let p = Math.max(1, current - 2); p <= Math.min(pdfDoc.numPages, current + 3); p++) renderPage(
                p);
        }

        $('#prev').click(() => $flipbook.turn('previous'));
        $('#next').click(() => $flipbook.turn('next'));
        $('#zoom-in').click(() => {
            userZoom += 0.15;
            reloadPages();
        });
        $('#zoom-out').click(() => {
            if (userZoom > 0.4) {
                userZoom -= 0.15;
                reloadPages();
            }
        });
        $('#fullscreen').click(() => {
            const el = document.documentElement;
            !document.fullscreenElement ? el.requestFullscreen() : document.exitFullscreen();
        });
        $('#sound-toggle').click(() => {
            soundEnabled = !soundEnabled;
            $('#sound-toggle').text(soundEnabled ? '🔊 Sound On' : '🔇 Sound Off');
        });
        $('#page-slider').on('input change', function() {
            $flipbook.turn('page', parseInt(this.value) || 1);
        });
        $(document).keydown(e => {
            if (e.key === 'ArrowLeft') $flipbook.turn('previous');
            if (e.key === 'ArrowRight') $flipbook.turn('next');
        });

        // SHARE button
        document.getElementById("share-btn").addEventListener("click", () => {
            const shareUrl = window.location.href; // current page link
            if (navigator.share) {
                navigator.share({
                    title: "Flipbook",
                    text: "Check out this PDF flipbook!",
                    url: shareUrl
                }).catch(err => console.log("Share failed:", err));
            } else {
                // fallback: copy to clipboard
                navigator.clipboard.writeText(shareUrl).then(() => {
                    alert("Link copied to clipboard!");
                });
            }
        });
    });
</script>
