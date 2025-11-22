<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FLip Book Modal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* (CSS remains the same) */
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin-bottom: 12px;
        }

        #flipbook-wrapper {
            position: relative;
            display: inline-block;
        }

        #flipbook {
            max-width: 1000px;
            max-height: 650px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            background: #fff;
        }

        #flipbook .page {
            max-width: 500px;
            max-height: 650px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        #flipbook .page canvas {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Top control buttons */
        .controls {
            margin: 12px 0;
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button,
        a.download-btn {
            padding: 8px 12px;
            border: none;
            background: #2e8b57;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        button:hover,
        a.download-btn:hover {
            background: #257046;
        }

        /* Navigation arrows */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 50;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            font-size: 22px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            user-select: none;
        }

        .nav-arrow.left {
            left: -30px;
        }

        .nav-arrow.right {
            right: -30px;
        }

        /* slider */
        #page-slider {
            width: 60%;
            margin: 20px auto 0 auto;
            display: block;
        }

        /* Bottom bar for share/download */
        .bottom-bar {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>

</head>

<body>
    <h1>Flipbook Modal Example</h1>
    <p>Click a button to load a different PDF (Ensure these paths exist on your server).</p>

    <button type="button" class="btn btn-primary loadImage" data-bs-toggle="modal" data-bs-target="#myModal"
        onclick="showModal(event, 'pdf/git-n-github-at-glance.pdf')">
        Open PDF 1 (Example Small)
    </button>

    <button type="button" class="btn btn-primary loadImage" data-bs-toggle="modal" data-bs-target="#myModal"
        onclick="showModal(event, 'pdf/BNP_Proposal_on_EC.pdf')">
        Open PDF 2 (Example Large)
    </button>

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="controls">
                        <button id="zoom-in">🔍 Zoom In</button>
                        <button id="zoom-out">🔎 Zoom Out</button>
                        <button id="fullscreen">⛶ Fullscreen</button>
                        <button id="sound-toggle">🔊 Sound On</button>
                    </div>

                    <div id="flipbook-wrapper">
                        <div id="flipbook">Loading...</div>
                        <div class="nav-arrow left" id="prev">◀</div>
                        <div class="nav-arrow right" id="next">▶</div>
                    </div>

                    <input id="page-slider" type="range" min="1" max="1" value="1">

                    <div class="bottom-bar">
                        <button id="share-btn">🔗 Share</button>
                        <a href="" download class="download-btn">⬇ Download PDF</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.js"></script>


<script>
    const bookWidth = 1000,
        bookHeight = 650,
        pageWidth = bookWidth / 2,
        pageHeight = bookHeight;
    let pdfDoc = null,
        userZoom = 1.0,
        soundEnabled = true;
    const flipSound = new Audio('https://www.soundjay.com/buttons/sounds/page-flip-01a.mp3'); 

    // Set the PDF.js worker path globally once
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

    // --- Core PDF Rendering Functions ---

    function renderPage(num) {
        if (!pdfDoc || num < 1 || num > pdfDoc.numPages) return;
        let canvas = document.getElementById('canvas-' + num);
        if (!canvas) return;

        // Clear any previous error message/hidden state
        $(canvas).removeClass('d-none').css('display', 'block'); 
        $(canvas).siblings('.error-message').remove();

        pdfDoc.getPage(num).then(function(page) {
            const unscaled = page.getViewport({
                scale: 1
            });
            const fitScale = (pageWidth / unscaled.width) * userZoom;
            const viewport = page.getViewport({
                scale: fitScale
            });
            
            // Set canvas dimensions
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = '100%';
            canvas.style.height = '100%'; 
            
            page.render({
                canvasContext: canvas.getContext('2d'),
                viewport
            });
        }).catch(pageErr => {
             console.error(`Error rendering page ${num}:`, pageErr);
             let $pageDiv = $(canvas).closest('.page');
             if ($pageDiv.length && !$pageDiv.find('.error-message').length) {
                $pageDiv.append('<div class="error-message" style="color:red; margin-top: 10px;">Error rendering page: ' + pageErr.message + '</div>');
                $(canvas).addClass('d-none'); 
             }
        });
    }

    function reloadPages(current) {
        let $flipbook = $('#flipbook');
        // This check prevents reloadPages from running if the PDF load failed (pdfDoc is null)
        if (!pdfDoc) return; 
        
        // This check is the source of the persistent error if turn.js state isn't cleaned up.
        // We'll rely on the aggressive cleanup in showModal to ensure this works.
        current = current || $flipbook.turn('page') || 1; 
        
        // Reload the current view and a few surrounding pages
        for (let p = Math.max(1, current - 2); p <= Math.min(pdfDoc.numPages, current + 3); p++) {
            renderPage(p);
        }
    }
    
    // --- Control and Event Binding Functions (Defined Globally) ---

    function attachControls(book) {
        // 1. Clear previous event listeners using namespaces for safety
        $('#prev, #next, #zoom-in, #zoom-out, #fullscreen, #sound-toggle, #page-slider, #share-btn').off('.flipbook');
        $(document).off('keydown.flipbook'); 

        // 2. Navigation (only bind if the book is initialized)
        if (book) {
            $('#prev').on('click.flipbook', () => book.turn('previous'));
            $('#next').on('click.flipbook', () => book.turn('next'));
            
            // Zoom Controls
            $('#zoom-in').on('click.flipbook', () => { 
                userZoom = Math.min(userZoom + 0.15, 3.0); 
                reloadPages(); 
            });
            $('#zoom-out').on('click.flipbook', () => { 
                userZoom = Math.max(userZoom - 0.15, 0.4); 
                reloadPages(); 
            });

            // Slider
            $('#page-slider').on('input change.flipbook', function() {
                const targetPage = parseInt(this.value) || 1;
                if (pdfDoc && targetPage >= 1 && targetPage <= pdfDoc.numPages) {
                    book.turn('page', targetPage);
                }
            });

            // Keyboard Navigation
            $(document).on('keydown.flipbook', e => {
                if ($('#myModal').hasClass('show')) {
                    if (e.key === 'ArrowLeft') book.turn('previous');
                    if (e.key === 'ArrowRight') book.turn('next');
                }
            });
        }
        
        // 3. Other Controls (Always bound)
        $('#fullscreen').on('click.flipbook', () => {
            const el = document.documentElement;
            !document.fullscreenElement ? el.requestFullscreen() : document.exitFullscreen();
        });
        
        $('#sound-toggle').on('click.flipbook', () => {
            soundEnabled = !soundEnabled;
            $('#sound-toggle').text(soundEnabled ? '🔊 Sound On' : '🔇 Sound Off');
        });
        
        $('#share-btn').on('click.flipbook', () => {
            const shareUrl = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: "Flipbook",
                    text: "Check out this PDF flipbook!",
                    url: shareUrl
                }).catch(err => console.log("Share failed:", err));
            } else {
                navigator.clipboard.writeText(shareUrl).then(() => {
                    alert("Link copied to clipboard!");
                });
            }
        });
    }

    // --- Modal and Load Workflow ---

    function showModal(event, filePath) {
        let $flipbook = $('#flipbook');
        
        // 1. CRITICAL CLEANUP STEP A: Destroy the old turn.js instance if it exists.
        if ($flipbook.data('turnJs')) {
            $flipbook.turn('destroy');
        }
        
        // 2. CRITICAL CLEANUP STEP B: Remove ALL jQuery data associated with the element.
        // This is the most aggressive way to clear leftover state that turn.js might be clinging to.
        $flipbook.removeData();

        // 3. Clear previous controls and reset state
        attachControls(null); 
        userZoom = 1.0; 
        pdfDoc = null; 
        $flipbook.empty().html('Loading...'); 
        
        $('#myModalLabel').text(filePath);
        $('#myModal .download-btn').attr('href', filePath);

        // 4. Load PDF immediately
        loadFlipbook(filePath);
    }

    function loadFlipbook(filePath) {
        let $flipbook = $('#flipbook');
        
        pdfjsLib.getDocument(filePath).promise.then(function(pdf) {
            pdfDoc = pdf;
            $flipbook.empty();
            console.log('PDF loaded with ' + pdf.numPages + ' pages');

            $('#page-slider').attr('max', pdf.numPages);

            // 1. Create page elements
            let pagesHtml = '';
            for (let i = 1; i <= pdf.numPages; i++) {
                pagesHtml += '<div class="page"><canvas id="canvas-' + i + '"></canvas></div>';
            }
            $flipbook.append(pagesHtml);

            // 2. Initialize turn.js
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
                        $('#page-slider').val(page);
                        renderPage(page);
                        renderPage(page + 1);
                        
                        if (soundEnabled) {
                            try {
                                flipSound.currentTime = 0;
                                flipSound.play();
                            } catch (e) {}
                        }
                    },
                    turned: function(event, page, view) {
                        reloadPages(page);
                    }
                }
            });

            // 3. Initial page render
            renderPage(1);
            if (pdf.numPages > 1) renderPage(2);
            
            // 4. Attach controls, passing the initialized turn.js instance
            attachControls($flipbook); 

        }).catch(err => {
            console.error('PDF load failed:', err);
            // Display error if PDF fails to load
            $('#flipbook').html('<p class="text-danger">Failed to load PDF: ' + err.message + '. Please ensure the PDF file path is correct or the file exists at that path.</p>');
        });
    }
</script>

</html>